<?php
namespace Tuum\Respond\Responder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Service\SessionStorageInterface;
use Tuum\Respond\Service\ViewData;

abstract class AbstractWithViewData
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
     * @var SessionStorageInterface
     */
    protected $session;

    /**
     * @var ViewData
     */
    protected $data;

    /**
     * returns a new ViewData as followings:
     * - retrieve from the session's flash data,
     * - retrieve from the container, or
     * - create a new ViewData.
     *
     * @return ViewData
     */
    private function retrieveViewData()
    {
        $data = null;
        // retrieving from the flash.
        if ($this->session) {
            $data = $this->session->getFlash(ViewData::MY_KEY);
            if ($data) {
                // if ViewData is taken from the session,
                // detach it from the object in the session.
                return clone($data);
            }
        }
        return new ViewData();
    }

    /**
     * @param ServerRequestInterface $request
     * @param null|ResponseInterface $response
     * @return AbstractWithViewData
     */
    protected function cloneWithRequest($request, $response = null)
    {
        $self = clone($this);
        $self->request  = $request;
        $self->response = $response;
        $self->data     = $self->retrieveViewData();
        return $self;
    }

    abstract public function withRequest(
        ServerRequestInterface $request,
        ResponseInterface $response = null
    );

    /**
     * @param SessionStorageInterface $session
     * @return View
     */
    public function withSession($session)
    {
        $self = clone($this);
        $self->session = $session;
        return $self;
    }

    /**
     * @param string|array $key
     * @param mixed        $value
     * @return $this
     */
    public function withFlashData($key, $value)
    {
        if (!isset($this->session)) {
            throw new \BadMethodCallException('SessionStorageInterface not defined.');
        }
        $this->session->setFlash($key, $value);
        return $this;
    }

    /**
     * @param string|array $key
     * @param mixed        $value
     * @return $this
     */
    public function with($key, $value = null)
    {
        $this->data = clone($this->data);
        $this->data->dataValue($key, $value);
        return $this;
    }

    /**
     * @param array $input
     * @return $this
     */
    public function withInputData(array $input)
    {
        $this->data = clone($this->data);
        $this->data->inputData($input);
        return $this;
    }

    /**
     * @param array $errors
     * @return $this
     */
    public function withInputErrors(array $errors)
    {
        $this->data = clone($this->data);
        $this->data->inputErrors($errors);
        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function withMessage($message)
    {
        $this->data = clone($this->data);
        $this->data->success($message);
        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function withAlertMsg($message)
    {
        $this->data = clone($this->data);
        $this->data->alert($message);
        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function withErrorMsg($message)
    {
        $this->data = clone($this->data);
        $this->data->error($message);
        return $this;
    }
}