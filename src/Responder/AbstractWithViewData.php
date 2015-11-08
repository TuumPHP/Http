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
     * initializes responders with $request, $response, $viewData,
     * and optionally $session. intended to be internal API used
     * by Responder object and tests. 
     * 
     * @param ServerRequestInterface  $request
     * @param ResponseInterface       $response
     * @param SessionStorageInterface $session
     * @param ViewData                $viewData
     * @return $this
     */
    public function withRequest(
        ServerRequestInterface $request,
        ResponseInterface $response,
        SessionStorageInterface $session,
        ViewData $viewData
    ) {
        $self           = clone($this);
        $self->request  = $request;
        $self->response = $response;
        $self->data     = $viewData;
        $self->session  = $session;

        return $self;
    }

    /**
     * copies request's attributes into data using keys.
     *
     * @api
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
     * @api
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
     * modifies viewData.
     *
     * @api
     * @param callable $closure
     * @return static
     */
    public function withViewData(callable $closure)
    {
        $self       = clone($this);
        $self->data = $closure($this->data);

        return $self;
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