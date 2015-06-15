<?php
namespace Tuum\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Http\Responder\Error;
use Tuum\Http\Responder\Redirect;
use Tuum\Http\Responder\View;

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
        return Error::forge($request, $response);
    }
}