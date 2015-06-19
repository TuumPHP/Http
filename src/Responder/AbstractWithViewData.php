<?php
namespace Tuum\Respond\Responder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\RequestHelper;
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
     * @var ViewData
     */
    protected $data;

    /**
     * returns a new ViewData as followings:
     * - retrieve from the session's flash data,
     * - retrieve from the container, or
     * - create a new ViewData.
     *
     * @param ServerRequestInterface $request
     * @return ViewData
     */
    protected function retrieveViewDta(ServerRequestInterface $request)
    {
        $data = null;
        // retrieving from the flash.
        if (RequestHelper::getSessionMgr($request)) {
            $data = RequestHelper::getFlash($request, ViewData::MY_KEY);
            if ($data) {
                // if ViewData is taken from the session,
                // detach it from the object in the session.
                $data = clone($data);
            }
        }
        // or get a new ViewData from container, or create a new one.
        if (!$data) {
            $data = RequestHelper::getService($this->request, ViewData::class) ?: new ViewData();
        }
        return $data;
    }

    /**
     * @param string|array $key
     * @param mixed        $value
     * @return $this
     */
    public function withFlashData($key, $value)
    {
        RequestHelper::setFlash($this->request, $key, $value);
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