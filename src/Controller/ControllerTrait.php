<?php
namespace Tuum\Respond\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Respond;

trait ControllerTrait
{
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
        if (!$this->responder) {
            $this->responder = Respond::getResponder();
        }
        return $this->_dispatch();
    }
    
    /**
     * must implement this method, which dispatches one of own method.
     *
     * @return ResponseInterface|null
     */
    abstract protected function _dispatch();
    
    /**
     * TODO: dispatchMethod confusing; so many dispatch* methods!
     * 
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