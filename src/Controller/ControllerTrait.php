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
        $this->setRequest($request);
        $this->setResponse($response);
        if (!$this->responder) {
            $this->responder = Respond::getResponder();
        }
        return $this->_execInternalMethods();
    }
    
    abstract function setRequest(ServerRequestInterface $request);

    abstract function setResponse(ResponseInterface $response);
    
    /**
     * must implement this method, which dispatches one of own method.
     *
     * @return ResponseInterface|null
     */
    abstract protected function _execInternalMethods();
    
    /**
     * @param string $method
     * @param array  $params
     * @return mixed
     */
    protected function _execMethodWithArgs($method, $params)
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