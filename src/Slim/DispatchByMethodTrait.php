<?php
namespace Tuum\Respond\Slim;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Responder\View;

trait DispatchByMethodTrait
{
    /**
     * @param $method
     * @param $params
     * @return ResponseInterface|null
     */
    abstract protected function dispatchMethod($method, $params);

    /**
     * @return View
     */
    abstract protected function view();

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface|null
     */
    protected function dispatch($request)
    {
        /*
         * set up request information
         */
        $params = (array)$request->getQueryParams();
        $method = $request->getMethod();
        if (strtoupper($method) === 'OPTIONS') {
            return $this->onOptions();
        }
        $method = 'on' . ucwords($method);
        if (!method_exists($this, $method)) {
            return null;
        }
        /*
         * invoke based on the method name i.e. onMethod(...)
         * also setup arguments from route parameters and get query.
         */
        return $this->dispatchMethod($method, $params);
    }

    /**
     * @return ResponseInterface
     */
    private function onOptions()
    {
        $refClass = new \ReflectionObject($this);
        $methods  = $refClass->getMethods();
        $options  = [];
        foreach ($methods as $method) {
            if (preg_match('/^on([_a-zA-Z0-9]+)$/', $method->getName(), $match)) {
                $options[] = strtoupper($match[1]);
            }
        }
        $options = array_unique($options);
        sort($options);
        $list = implode(',', $options);
        $this->view()->asResponse('', 200, ['Allow' => $list]);
    }

}