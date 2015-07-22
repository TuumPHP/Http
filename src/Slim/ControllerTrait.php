<?php
namespace Tuum\Respond\Slim;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\RequestHelper;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder\Error;
use Tuum\Respond\Responder\Redirect;
use Tuum\Respond\Responder\View;

trait ControllerTrait
{
    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var array
     */
    protected $arguments = [];
    
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param array                  $args
     * @return null|ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->request   = $request;
        $this->response  = $response;
        $this->arguments = $args;
        if (strtoupper($request->getMethod()) === 'HEAD') {
            return $this->onHead();
        }

        return $this->dispatch();
    }

    /**
     * @return ResponseInterface|null
     */
    abstract protected function dispatch();

    /**
     * @return string
     */
    protected function getMethod()
    {
        return $this->getMethod();
    }

    /**
     * @return string
     */
    protected function getPathInfo()
    {
        return RequestHelper::getPathInfo($this->request);
    }

    /**
     * @return array
     */
    protected function getQueryParams()
    {
        return array_merge($this->request->getQueryParams(), $this->arguments);
    }

    /**
     * @return View
     */
    protected function view()
    {
        return Respond::view($this->request, $this->response);
    }

    /**
     * @return Redirect
     */
    protected function redirect()
    {
        return Respond::redirect($this->request, $this->response);
    }

    /**
     * @return Error
     */
    protected function error()
    {
        return Respond::error($this->request, $this->response);
    }

    /**
     * @return null|ResponseInterface
     */
    protected function onHead()
    {
        $this->request = $this->request->withMethod('GET');
        $response      = $this->dispatch();
        if ($response) {
            $response->getBody()->rewind();
            $response->getBody()->write('');

            return $response;
        }

        return null;
    }

    /**
     * @param string $method
     * @param array  $params
     * @return mixed
     */
    protected function dispatchMethod($method, $params)
    {
        $refMethod = new \ReflectionMethod($this, $method);
        $refArgs   = $refMethod->getParameters();
        $list      = array();
        foreach ($refArgs as $arg) {
            $key        = $arg->getPosition();
            $name       = $arg->getName();
            $opt        = $arg->isOptional() ? $arg->getDefaultValue() : null;
            $val        = isset($params[$name]) ? $params[$name] : $opt;
            $list[$key] = $val;
        }
        $refMethod->setAccessible(true);

        return $refMethod->invokeArgs($this, $list);
    }
}