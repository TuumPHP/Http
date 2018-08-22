<?php
namespace Tuum\Respond\Responder;

use Tuum\Respond\Interfaces\PayloadInterface;

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
class Payload implements PayloadInterface
{
    /**
     * @var bool
     */
    private $hasError = false;

    /**
     * @var string
     */
    private $errorType = PayloadInterface::ERROR_TYPE_SUCCESS;

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
     * @return Payload
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
     * @return Payload
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
     * @return Payload
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
     * @return Payload
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
     * @return Payload
     */
    public function setSuccess($message)
    {
        return $this->setMessage(PayloadInterface::MESSAGE_SUCCESS, $message);
    }

    /**
     * @param string $message
     * @return Payload
     */
    public function setAlert($message)
    {
        return $this->setMessage(PayloadInterface::MESSAGE_ALERT, $message);
    }

    /**
     * @param string $message
     * @return Payload
     */
    public function setError($message = null)
    {
        $this->setErrorType(PayloadInterface::ERROR_TYPE_ERROR);
        return $this->setMessage(PayloadInterface::MESSAGE_ERROR, $message);
    }
    
    /**
     * @param string $message
     * @return PayloadInterface
     */
    public function setCritical($message = null)
    {
        $this->setErrorType(PayloadInterface::ERROR_TYPE_CRITICAL);
        return $this->setMessage(PayloadInterface::MESSAGE_ERROR, $message);
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