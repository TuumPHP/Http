<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\RequestHelper;
use Tuum\Respond\Respond;

/**
 * set routes and dispatch.
 *
 * @param ServerRequestInterface $req
 * @return ResponseInterface
 */
return function ($request) {

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    $all = function ($request) {
        return Respond::view($request)
            ->asView('index');
    };

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    $jump = function ($request) {
        return Respond::view($request)
            ->asView('jump');
    };

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    $jumper = function ($request) {
        return Respond::redirect($request)
            ->withMessage('redirected back!')
            ->withInputData(['jumped' => 'redirected text'])
            ->withInputErrors(['jumped' => 'redirected error message'])
            ->toPath('jump');
    };

    /**
     * @throw \Exception
     */
    $throw = function () {
        throw new \Exception('always throws exception');
    };

    /**
     * $routes to aggregate all the routes.
     */
    $routes = array(
        '/jump'   => $jump,
        '/jumper' => $jumper,
        '/throw'  => $throw,
        '/'       => $all,
    );

    /**
     * main routine: route match!!!
     */
    $response = null;
    $path     = RequestHelper::getPathInfo($request);
    foreach ($routes as $root => $app) {
        if ($root === $path) {
            $response = $app($request);
            break;
        }
    }
    return $response;
};
