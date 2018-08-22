<?php
namespace Tuum\Respond\Interfaces;

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
interface PayloadInterface
{
    /**
     * a key string for storing view-data.
     */
    const MY_KEY = '-view-data-key';

    /*
     * message types.
     */
    const MESSAGE_SUCCESS = 'message';
    const MESSAGE_ALERT   = 'alert';
    const MESSAGE_ERROR   = 'error';

    /*
     * error types. 
     */
    const ERROR_TYPE_SUCCESS  = 'success';
    const ERROR_TYPE_ERROR    = 'error';
    const ERROR_TYPE_CRITICAL = 'critical';

    /**
     * set data for Data helper.
     *
     * @param string|array $key
     * @param mixed        $value
     * @return PayloadInterface
     */
    public function setData($key, $value = null);

    /**
     * @return array
     */
    public function getData();

    /**
     * sets input value, like $_POST, for Inputs helper.
     *
     * @param array $value
     * @return PayloadInterface
     */
    public function setInput(array $value);

    /**
     * @return array
     */
    public function getInput();

    /**
     * sets input errors, such as validation error messages.
     * for Errors helper.
     *
     * @param array $errors
     * @return PayloadInterface
     */
    public function setInputErrors(array $errors);

    /**
     * @return array
     */
    public function getInputErrors();

    /**
     * a generic message method for Message helper.
     * use success, alert, and error methods.
     *
     * @param string $type
     * @param string $message
     * @return PayloadInterface
     */
    public function setMessage($type, $message);

    /**
     * @return array
     */
    public function getMessages();

    /**
     * @param string $message
     * @return PayloadInterface
     */
    public function setSuccess($message);

    /**
     * @param string $message
     * @return PayloadInterface
     */
    public function setAlert($message);

    /**
     * @param string $message
     * @return PayloadInterface
     */
    public function setError($message = null);

    /**
     * @param string $message
     * @return PayloadInterface
     */
    public function setCritical($message = null);
    
    /**
     * @return bool
     */
    public function hasError();

    /**
     * @return string
     */
    public function getErrorType();
}