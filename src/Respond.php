<?php
namespace Tuum\Respond;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Interfaces\PayloadInterface;
use Tuum\Respond\Responder\Error;
use Tuum\Respond\Responder\Redirect;
use Tuum\Respond\Responder\View;

class Respond
{
    /**
     * @var Responder
     */
    private static $responder;

    /**
     * @return Responder
     */
    public static function getResponder()
    {
        return self::$responder;
    }
    
    public static function setResponder(Responder $responder)
    {
        self::$responder = $responder;
    }

    /**
     * get the responder of $name.
     *
     * @param string                 $name
     * @param ServerRequestInterface $request
     * @return View|Redirect|Error
     */
    private static function _getResponder($name, $request)
    {
        /**
         * 1. get responder from the request' attribute.
         *
         * @var Responder $responder
         */
        if (!$responder = self::getResponder()) {
            throw new \BadMethodCallException;
        }

        /**
         * 2. return responder with $name.
         */

        return $responder->$name($request);
    }

    /**
     * @return PayloadInterface
     */
    public static function getPayload()
    {
        return self::getResponder()->getPayload();
    }
    
    /**
     * get a view responder, Responder\View.
     *
     * @param ServerRequestInterface $request
     * @return View
     */
    public static function view($request)
    {
        return self::_getResponder('view', $request);
    }

    /**
     * get a redirect responder, Responder\Redirect.
     *
     * @param ServerRequestInterface $request
     * @return Redirect
     */
    public static function redirect($request)
    {
        return self::_getResponder('redirect', $request);
    }

    /**
     * get an error responder, Responder\Error.
     *
     * @param ServerRequestInterface $request
     * @return Error
     */
    public static function error($request)
    {
        return self::_getResponder('error', $request);
    }

    /**
     * @return \Tuum\Respond\Interfaces\SessionStorageInterface
     */
    public static function session()
    {
        return self::getResponder()->session();
    }
}