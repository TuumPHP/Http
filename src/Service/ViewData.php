<?php
namespace Tuum\Respond\Service;

use Tuum\Respond\Interfaces\ViewDataInterface;

/**
 * Class ViewData
 *
 * a data transfer object for responder to Tuum/Form helpers used in templates.
 *
 * types data are:
 * - data (Data helper),
 * - message (Message helper),
 * - inputData (Inputs helper),
 * - inputErrors (Errors helper), and
 * - rawData (raw values are populated).
 *
 * @package Tuum\Respond
 */
class ViewData implements ViewDataInterface
{
    /**
     * @var bool
     */
    private $hasError = false;

    /**
     * @var string
     */
    private $errorType = ViewDataInterface::ERROR_TYPE_SUCCESS;

    /**
     * @var array
     */
    private $data = [];

    /**
     * @var array
     */
    private $messages = [];

    /**
     * @var array
     */
    private $inputData = [];

    /**
     * @var bool
     */
    private $hasInput = false;

    /**
     * @var array
     */
    private $inputErrors = [];

    /**
     * set data for Data helper.
     *
     * @param string|array $key
     * @param mixed        $value
     * @return ViewData
     */
    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * sets input value, like $_POST, for Inputs helper.
     *
     * @param array $value
     * @return ViewData
     */
    public function setInput(array $value)
    {
        $this->inputData = $value;
        $this->hasInput = true;

        return $this;
    }

    /**
     * @return array
     */
    public function getInput()
    {
        return $this->inputData;
    }

    /**
     * @return bool
     */
    public function hasInput()
    {
        return $this->hasInput;
    }

    /**
     * sets input errors, such as validation error messages.
     * for Errors helper.
     *
     * @param array $errors
     * @return ViewData
     */
    public function setInputErrors(array $errors)
    {
        $this->inputErrors = $errors;

        return $this;
    }

    /**
     * @return array
     */
    public function getInputErrors()
    {
        return $this->inputErrors;
    }

    /**
     * a generic message method for Message helper.
     * use success, alert, and error methods.
     *
     * @param string $type
     * @param string $message
     * @return ViewData
     */
    public function setMessage($type, $message)
    {
        if (!is_null($message)) {
            $this->messages[] = [
                'message' => $message,
                'type'    => $type,
            ];
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param string $message
     * @return ViewData
     */
    public function setSuccess($message)
    {
        return $this->setMessage(ViewDataInterface::MESSAGE_SUCCESS, $message);
    }

    /**
     * @param string $message
     * @return ViewData
     */
    public function setAlert($message)
    {
        return $this->setMessage(ViewDataInterface::MESSAGE_ALERT, $message);
    }

    /**
     * @param string $message
     * @return ViewData
     */
    public function setError($message = null)
    {
        $this->setErrorType(ViewDataInterface::ERROR_TYPE_ERROR);
        return $this->setMessage(ViewDataInterface::MESSAGE_ERROR, $message);
    }
    
    /**
     * @param string $message
     * @return ViewDataInterface
     */
    public function setCritical($message = null)
    {
        $this->setErrorType(ViewDataInterface::ERROR_TYPE_CRITICAL);
        return $this->setMessage(ViewDataInterface::MESSAGE_ERROR, $message);
    }

    /**
     * @param string $type
     */
    private function setErrorType($type)
    {
        $this->hasError = true;
        $this->errorType = $type;
    }
    
    /**
     * @return bool
     */
    public function hasError()
    {
        return $this->hasError;
    }

    /**
     * @return string
     */
    public function getErrorType()
    {
        return $this->errorType;
    }
}