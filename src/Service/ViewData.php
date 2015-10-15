<?php
namespace Tuum\Respond\Service;

class ViewData
{
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
     */
    public function setRawData($key, $value)
    {
        $this->rawData[$key] = $value;
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
     * @param string|array $key
     * @param mixed  $value
     */
    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * sets input value, like $_POST.
     *
     * @param array $value
     */
    public function inputData(array $value)
    {
        $this->inputData = $value;
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
     *
     * @param array $errors
     */
    public function inputErrors(array $errors)
    {
        $this->inputErrors = $errors;
    }

    /**
     * @return array
     */
    public function getInputErrors()
    {
        return $this->inputErrors;
    }

    /**
     * @param string $message
     * @param string $type
     * @return array
     */
    public function message($message, $type)
    {
        $this->messages[] = [
            'message' => $message,
            'type'    => $type,
        ];
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
     * @return array
     */
    public function success($message)
    {
        return $this->message($message, self::MESSAGE_SUCCESS);
    }

    /**
     * @param string $message
     * @return array
     */
    public function alert($message)
    {
        return $this->message($message, self::MESSAGE_ALERT);
    }

    /**
     * @param string $message
     * @return array
     */
    public function error($message)
    {
        return $this->message($message, self::MESSAGE_ERROR);
    }
}