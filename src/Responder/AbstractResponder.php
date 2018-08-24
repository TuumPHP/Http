<?php
namespace Tuum\Respond\Responder;

use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder;
use Tuum\Respond\Service\SessionStorage;

/**
 * Class AbstractResponder
 *
 * @package Tuum\Respond
 */
abstract class AbstractResponder
{
    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * @var Responder
     */
    protected $responder;

    /**
     * 
     */
    public function __construct()
    {
    }

    /**
     * @param ServerRequestInterface $request
     * @param Responder              $responder
     * @return $this
     */
    public function start(ServerRequestInterface $request, Responder $responder): self
    {
        $this->request   = $request;
        $this->responder = $responder;
        if (!Respond::getPayload($request)) {
            Respond::setPayload($request, $responder->session()->getPayload());
        }

        return $this;
    }

    /**
     * @param string|array $key
     * @param mixed        $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        $this->responder->getPayload($this->request)->setData($key, $value);
        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setSuccess($message)
    {
        $this->responder->getPayload($this->request)->setSuccess($message);
        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setAlert($message)
    {
        $this->responder->getPayload($this->request)->setAlert($message);
        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setError($message)
    {
        $this->responder->getPayload($this->request)->setError($message);
        return $this;
    }

    /**
     * @param array $input
     * @return $this
     */
    public function setInput(array $input)
    {
        $this->responder->getPayload($this->request)->setInput($input);
        return $this;
    }

    /**
     * @param array $messages
     * @return $this
     */
    public function setInputErrors(array $messages)
    {
        $this->responder->getPayload($this->request)->setInputErrors($messages);
        return $this;
    }
}