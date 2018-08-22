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
     * @param ResponseInterface|null $response
     * @return View|Redirect|Error
     */
    private static function _getResponder($name, $request, $response)
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

        return $responder->$name($request, $response);
    }

    /**
     * @return PayloadInterface
     */
    public static function getViewData()
    {
        return self::getResponder()->getViewData();
    }
    
    /**
     * get a view responder, Responder\View.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface|null $response
     * @return View
     */
    public static function view($request, $response = null)
    {
        return self::_getResponder('view', $request, $response);
    }

    /**
     * get a redirect responder, Responder\Redirect.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface|null $response
     * @return Redirect
     */
    public static function redirect($request, $response = null)
    {
        return self::_getResponder('redirect', $request, $response);
    }

    /**
     * get an error responder, Responder\Error.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface|null $response
     * @return Error
     */
    public static function error($request, $response = null)
    {
        return self::_getResponder('error', $request, $response);
    }

    /**
     * @return \Tuum\Respond\Interfaces\SessionStorageInterface
     */
    public static function session()
    {
        return self::getResponder()->session();
    }
}