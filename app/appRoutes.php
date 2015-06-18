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
return function($req) {

    /**
     * @param ServerRequestInterface $req
     * @return ResponseInterface
     */
    $all = function($req) {
        return Respond::view($req)
            ->asView('index');
    };

    /**
     * @param ServerRequestInterface $req
     * @return ResponseInterface
     */
    $jump = function($req) {
        return Respond::view($req)
            ->asView('jump');
    };

    /**
     * @param ServerRequestInterface $req
     * @return ResponseInterface
     */
    $jumper = function($req) {
        return Respond::redirect($req)
            ->withMessage('redirected back!')
            ->withInputData(['jumped' => 'redirected text'])
            ->withInputErrors(['jumped' => 'redirected error message'])
            ->toPath('jump');
    };

    /**
     * @throw \Exception
     */
    $throw = function() {
        throw new \Exception('always throws exception');
    };

    /**
     *
     */
    $routes = array(
        '/jump' => $jump,
        '/jumper' => $jumper,
        '/throw' => $throw,
        '/' => $all,
    );

    $res    = null;
    $path   = RequestHelper::getPathInfo($req);
    foreach($routes as $root => $app) {
        if (!$root) {
            $res = $app($req);
            break;
        }
        if ($root === $path) {
            $res = $app($req);
            break;
        }
    }
    return $res;
};
