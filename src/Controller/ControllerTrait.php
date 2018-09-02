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
     * @return ResponseInterface|null
     */
    protected function dispatch($request)
    {
        $this->setRequest($request);
        if (!$this->responder) {
            $this->responder = Respond::getResponder();
        }
        return $this->_execInternalMethods();
    }
    
    abstract function setRequest(ServerRequestInterface $request);

    abstract function getRequest(): ServerRequestInterface;

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
     * @throws \ReflectionException
     */
    protected function _execMethodWithArgs($method, $params)
    {
        $params = $params + $this->getRequest()->getQueryParams();
        $refMethod = new \ReflectionMethod($this, $method);
        $refArgs   = $refMethod->getParameters();
        $arguments = array();
        foreach ($refArgs as $arg) {
            $key             = $arg->getPosition();
            $name            = $arg->getName();
            $opt             = $arg->isOptional() ? $arg->getDefaultValue() : null;
            $val             = $params[$name] ?? $this->getRequest()->getAttribute($name, $opt);
            $arguments[$key] = $val;
        }
        $refMethod->setAccessible(true);
        return $refMethod->invokeArgs($this, $arguments);
    }
}