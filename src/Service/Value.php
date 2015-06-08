<?php
namespace Tuum\Http\Service;

class Value
{
    /*
     * constants for data types.
     */
    const MESSAGE = '-message-view';
    const INPUTS = '-input-view';
    const ERRORS = '-errors-view';
    const URI = '-uri-view';

    /*
     * message types. 
     */
    const MESSAGE_SUCCESS = 'message';
    const MESSAGE_ALERT = 'alert';
    const MESSAGE_ERROR = 'error';

    /**
     * @param string $message
     * @param string $type
     * @return array
     */
    public static function message($message, $type)
    {
        return [
            'message' => $message,
            'type'    => $type,
        ];
    }

    /**
     * @param string $message
     * @return array
     */
    public static function success($message)
    {
        return self::message($message, self::MESSAGE_SUCCESS);
    }

    /**
     * @param string $message
     * @return array
     */
    public static function alert($message)
    {
        return self::message($message, self::MESSAGE_ALERT);
    }

    /**
     * @param string $message
     * @return array
     */
    public static function error($message)
    {
        return self::message($message, self::MESSAGE_ERROR);
    }
}