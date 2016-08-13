<?php
/**
 * Created by PhpStorm.
 * User: asao
 * Date: 2016/02/18
 * Time: 15:11
 */
namespace Tuum\Respond\Interfaces;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Service\ViewHelper;


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
interface ViewDataInterface
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

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ViewDataInterface
     */
    public function withRequest($request, $response);

    /**
     * @return ServerRequestInterface
     */
    public function getRequest();

    /**
     * @return ResponseInterface
     */
    public function getResponse();

    /**
     * @return ViewHelper
     */
    public function createHelper();
        
    /**
     * set a raw data.
     *
     * @param string $key
     * @param mixed  $value
     * @return ViewDataInterface
     */
    public function setExtra($key, $value);

    /**
     * get a raw data.
     *
     * @return array
     */
    public function getExtra();

    /**
     * set data for Data helper.
     *
     * @param string|array $key
     * @param mixed        $value
     * @return ViewDataInterface
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
     * @return ViewDataInterface
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
     * @return ViewDataInterface
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
     * @param string $message
     * @param string $type
     * @return ViewDataInterface
     */
    public function setMessage($message, $type);

    /**
     * @return array
     */
    public function getMessages();

    /**
     * @param string $message
     * @return ViewDataInterface
     */
    public function setSuccess($message);

    /**
     * @param string $message
     * @return ViewDataInterface
     */
    public function setAlert($message);

    /**
     * @param string $message
     * @return ViewDataInterface
     */
    public function setError($message);
}