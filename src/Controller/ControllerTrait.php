<?php
namespace Tuum\Respond\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder;

trait ControllerTrait
{
    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var Responder
     */
    protected $responder;
    
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
     * @return Responder
     */
    protected function getResponder()
    {
        return $this->responder;
    }

    /**
     * @param Responder $responder
     */
    protected function setResponder($responder)
    {
        $this->responder = $responder;
    }

    /**
     * @param null|string $name
     * @return array|null|object
     */
    protected function getPost($name = null)
    {
        if (is_null($name)) {
            return $this->request->getParsedBody();
        }
        $post = $this->request->getParsedBody();
        return array_key_exists($name, $post) ? $post[$name] : null;
    }

    /**
     * @return UploadedFileInterface[]
     */    
    protected function getUploadFiles()
    {
        return $this->request->getUploadedFiles();
    }
    
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