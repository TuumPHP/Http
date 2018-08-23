<?php
namespace Tuum\Respond\Responder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var SessionStorage
     */
    protected $session;

    /**
     * @param SessionStorage $session
     */
    public function __construct(SessionStorage $session)
    {
        $this->session = $session;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @return $this
     */
    public function start(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->request = $request;
        $this->response = $response;

        return $this;
    }

    /**
     * @param string|array $key
     * @param mixed        $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        $this->session->getPayload()->setData($key, $value);
        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setSuccess($message)
    {
        $this->session->getPayload()->setSuccess($message);
        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setAlert($message)
    {
        $this->session->getPayload()->setAlert($message);
        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setError($message)
    {
        $this->session->getPayload()->setError($message);
        return $this;
    }

    /**
     * @param array $input
     * @return $this
     */
    public function setInput(array $input)
    {
        $this->session->getPayload()->setInput($input);
        return $this;
    }

    /**
     * @param array $messages
     * @return $this
     */
    public function setInputErrors(array $messages)
    {
        $this->session->getPayload()->setInputErrors($messages);
        return $this;
    }
}