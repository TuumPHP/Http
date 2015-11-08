<?php
namespace Tuum\Respond\Responder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\RequestHelper;
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
     * @param ServerRequestInterface  $request
     * @param ResponseInterface       $response
     * @param SessionStorageInterface $session
     * @param ViewData                $viewData
     * @return $this
     */
    public function withRequest(
        ServerRequestInterface $request,
        ResponseInterface $response,
        SessionStorageInterface $session = null,
        ViewData $viewData = null
    ) {
        $self           = clone($this);
        $self->request  = $request;
        $self->response = $response;
        if (!$self->session) {
            $self->session = $session ?: RequestHelper::getSessionMgr($request);
        }
        $self->data = $viewData ?: $self->retrieveViewData();

        return $self;
    }

    /**
     * copies request's attributes into data using keys.
     *
     * @param string $arg
     * @return $this
     */
    public function withReqAttribute($arg)
    {
        $args = func_get_args();
        $self = clone($this);
        foreach ($args as $key) {
            $self->data->setData($key, $this->request->getAttribute($key));
        }

        return $self;
    }

    /**
     * @param string       $key
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
        $this->data->setData($key, $value);

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