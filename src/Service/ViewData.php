<?php
namespace Tuum\Http\Service;

class ViewData
{
    /**
     * @var array
     */
    private $data = [];

    const MY_KEY = '-view-data-key';

    /*
     * constants for data types.
     */
    const DATA    = '-data-view';
    const MESSAGE = '-message-view';
    const INPUTS  = '-input-view';
    const ERRORS  = '-errors-view';

    /*
     * message types. 
     */
    const MESSAGE_SUCCESS = 'message';
    const MESSAGE_ALERT   = 'alert';
    const MESSAGE_ERROR   = 'error';

    /**
     * get all raw data.
     *
     * @return array
     */
    public function getRawData()
    {
        return $this->data;
    }

    /**
     * set all raw data.
     *
     * @param array $data
     * @param bool  $replace
     */
    public function setRawData(array $data, $replace = false)
    {
        if (!$replace) {
            $data = array_merge($this->data, $data);
        }
        $this->data = $data;
    }

    /**
     * get a raw data.
     *
     * @param string     $key
     * @param null|mixed $alt
     * @return mixed
     */
    public function get($key, $alt = null)
    {
        return array_key_exists($key, $this->data) ? $this->data[$key] : $alt;
    }

    /**
     * set a raw data.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function dataValue($key, $value = null)
    {
        if (!array_key_exists(self::DATA, $this->data)) {
            $this->data[self::DATA] = [];
        }
        if (is_array($key)) {
            $this->data[self::DATA] = array_merge($this->data[self::DATA], $key);
        } else {
            $this->data[self::DATA][$key] = $value;
        }
    }

    /**
     * sets input value, like $_POST.
     *
     * @param array $value
     */
    public function inputData(array $value)
    {
        $this->data[self::INPUTS] = $value;
    }

    /**
     * sets input errors, such as validation error messages.
     *
     * @param array $errors
     */
    public function inputErrors(array $errors)
    {
        $this->data[self::ERRORS] = $errors;
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