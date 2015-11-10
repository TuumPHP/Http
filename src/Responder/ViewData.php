<?php
namespace Tuum\Respond\Responder;

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
class ViewData
{
    /**
     * @var int
     */
    private $status;

    /**
     * @var string
     */
    private $view_file;

    /**
     * @var array
     */
    private $data = [];

    /**
     * @var array
     */
    private $rawData = [];

    /**
     * @var array
     */
    private $messages = [];

    /**
     * @var array
     */
    private $inputData = [];

    /**
     * @var array
     */
    private $inputErrors = [];

    const MY_KEY = '-view-data-key';

    /*
     * message types. 
     */
    const MESSAGE_SUCCESS = 'message';
    const MESSAGE_ALERT = 'alert';
    const MESSAGE_ERROR = 'error';

    /**
     * set a raw data.
     *
     * @param string $key
     * @param mixed  $value
     * @return $this
     */
    public function setRawData($key, $value)
    {
        $this->rawData[$key] = $value;

        return $this;
    }

    /**
     * get a raw data.
     *
     * @return array
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * set data for Data helper.
     *
     * @param string|array $key
     * @param mixed        $value
     * @return $this
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
     * @return $this
     */
    public function setInputData(array $value)
    {
        $this->inputData = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getInputData()
    {
        return $this->inputData;
    }

    /**
     * sets input errors, such as validation error messages.
     * for Errors helper.
     *
     * @param array $errors
     * @return $this
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
     * @param string $message
     * @param string $type
     * @return $this
     */
    public function setMessage($message, $type)
    {
        $this->messages[] = [
            'message' => $message,
            'type'    => $type,
        ];

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
     * @return $this
     */
    public function setSuccess($message)
    {
        return $this->setMessage($message, self::MESSAGE_SUCCESS);
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setAlert($message)
    {
        return $this->setMessage($message, self::MESSAGE_ALERT);
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setError($message)
    {
        return $this->setMessage($message, self::MESSAGE_ERROR);
    }

    /**
     * @param string $view_file
     * @return ViewData
     */
    public function setViewFile($view_file)
    {
        $this->view_file = $view_file;

        return $this;
    }

    /**
     * @return string
     */
    public function getViewFile()
    {
        return $this->view_file;
    }

    /**
     * @param int $status
     * @return ViewData
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }
}