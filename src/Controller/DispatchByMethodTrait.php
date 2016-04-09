<?php
namespace Tuum\Respond\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Helper\ReqAttr;

trait DispatchByMethodTrait
{
    use ControllerTrait;

    /**
     * @return null|ResponseInterface
     */
    protected function _dispatch()
    {
        /*
         * set up request information
         */
        $params = (array)$this->request->getQueryParams();
        $method = ReqAttr::getMethod($this->request);
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

        return $this->getResponse()->withHeader('Allow', $list);
    }
}