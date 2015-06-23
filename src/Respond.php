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
     * get a view responder, Responder\View.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface|null $response
     * @return View
     */
    public static function view($request, $response = null)
    {
        /** @var View $view */
        if ($view = RequestHelper::getService($request, View::class)) {
            return $view->withRequest($request, $response);
        }
        return View::forge($request, $response);
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
        /** @var Redirect $redirect */
        if ($redirect = RequestHelper::getService($request, Redirect::class)) {
            return $redirect->withRequest($request, $response);
        }
        return Redirect::forge($request, $response);
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
        /** @var Error $error */
        if ($error = RequestHelper::getService($request, Error::class)) {
            return $error->withRequest($request, $response);
        }
        return Error::forge($request, $response);
    }
}