<?php
namespace Tuum\Respond\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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
     * call this dispatch method to respond.
     * 
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @return ResponseInterface|null
     */
    protected function dispatch($request, $response)
    {
        $this->request  = $request;
        $this->response = $response;
        return $this->_dispatch();
    }
    
    /**
     * @return ResponseInterface|null
     */
    abstract protected function _dispatch();

    /**
     * @return ServerRequestInterface
     */
    protected function getRequest()
    {
        return $this->request;
    }

    /**
     * @param null|string $string
     * @return ResponseInterface
     */    
    protected function getResponse($string = null)
    {
        if (is_string($string)) {
            $this->response->getBody()->write($string);
        }
        return $this->response;
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
        $arguments = array();
        foreach ($refArgs as $arg) {
            $key             = $arg->getPosition();
            $name            = $arg->getName();
            $opt             = $arg->isOptional() ? $arg->getDefaultValue() : null;
            $val             = isset($params[$name]) ? $params[$name] : $opt;
            $arguments[$key] = $val;
        }
        $refMethod->setAccessible(true);
        return $refMethod->invokeArgs($this, $arguments);
    }
}