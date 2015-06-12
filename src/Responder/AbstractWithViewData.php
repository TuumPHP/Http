<?php
namespace Tuum\Http\Responder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Http\RequestHelper;
use Tuum\Http\Service\ViewData;

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
        $this->data->dataValue($key, $value);
        return $this;
    }

    /**
     * @param array $input
     * @return $this
     */
    public function withInputData(array $input)
    {
        $this->data->inputData($input);
        return $this;
    }

    /**
     * @param array $errors
     * @return $this
     */
    public function withInputErrors(array $errors)
    {
        $this->data->inputErrors($errors);
        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function withMessage($message)
    {
        $this->data->success($message);
        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function withAlertMsg($message)
    {
        $this->data->alert($message);
        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function withErrorMsg($message)
    {
        $this->data->error($message);
        return $this;
    }
}