<?php
namespace Tuum\Http\Service;

class ViewData
{
    /**
     * @var array
     */
    private $data = [];

    /*
     * constants for data types.
     */
    const MESSAGE = '-message-view';
    const INPUTS = '-input-view';
    const ERRORS = '-errors-view';

    /*
     * message types. 
     */
    const MESSAGE_SUCCESS = 'message';
    const MESSAGE_ALERT = 'alert';
    const MESSAGE_ERROR = 'error';

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * @param string      $key
     * @param null|mixed  $alt
     * @return mixed
     */
    public function get($key, $alt = null)
    {
        return array_key_exists($key, $this->data) ? $this->data[$key] : $alt;
    }

    /**
     * sets input value, like $_POST.
     *
     * @param array $value
     */
    public function inputData(array $value)
    {
        $this->set(self::INPUTS, $value);
    }

    /**
     * sets input errors, such as validation error messages.
     *
     * @param array $errors
     */
    public function inputErrors(array $errors)
    {
        $this->set(self::ERRORS, $errors);
    }

    /**
     * @param string $message
     * @param string $type
     * @return array
     */
    public function message($message, $type)
    {
        if (!array_key_exists(self::MESSAGE, $this->data)) {
            $this->data[self::MESSAGE] = [];
        }
        $this->data[self::MESSAGE][] = [
            'message' => $message,
            'type'    => $type,
        ];
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