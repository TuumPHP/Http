<?php
namespace Tuum\Respond;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Responder\Error;
use Tuum\Respond\Responder\Redirect;
use Tuum\Respond\Responder\View;

class Respond
{
    /**
     * get the responder of $name.
     *
     * @param string                 $name
     * @param ServerRequestInterface $request
     * @param ResponseInterface|null $response
     * @return mixed
     */
    private static function _getResponder($name, $request, $response)
    {
        /**
         * 1. get responder from the request' attribute.
         *
         * @var Responder $responder
         */
        if (!$responder = RequestHelper::getResponder($request)) {
            throw new \BadMethodCallException;
        }

        /**
         * 2. return responder with $name.
         */

        return $responder->$name($request, $response);
    }

    /**
     * get responder object while updating viewData with $closure.
     *
     * @param ServerRequestInterface $request
     * @param callable               $closure
     * @return ServerRequestInterface
     */
    public static function with($request, callable $closure = null)
    {
        if (!$responder = RequestHelper::getResponder($request)) {
            throw new \BadMethodCallException;
        }
        if ($closure && is_callable($closure)) {
            $responder = $responder->withViewData($closure);
            $request   = RequestHelper::withResponder($request, $responder);
        }

        return $request;
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
}