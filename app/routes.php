<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\RequestHelper;
use Tuum\Respond\Respond;

/**
 * @param ServerRequestInterface $req
 * @param ResponseInterface      $res
 * @return ResponseInterface
 */
$all = function($req, $res) {
    return Respond::view($req, $res)
        ->asView('index');
};

/**
 * @param ServerRequestInterface $req
 * @param ResponseInterface      $res
 * @return ResponseInterface
 */
$jump = function($req, $res) {
    return Respond::view($req, $res)
        ->asView('jump');
};

/**
 * @param ServerRequestInterface $req
 * @param ResponseInterface      $res
 * @return ResponseInterface
 */
$jumper = function($req, $res) {
    return Respond::redirect($req, $res)
        ->withMessage('jumped back!')
        ->withInputData(['jumped' => 'done'])
        ->withInputErrors(['jumped' => 'error'])
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

/**
 * @param ServerRequestInterface $req
 * @param ResponseInterface      $res
 * @return ResponseInterface
 */
return function($req, $res) use ($routes) {
    
    $path   = RequestHelper::getPathInfo($req);
    foreach($routes as $root => $app) {
        if (!$root) {
            $res = $app($req, $res);
            break;
        }
        if ($root === $path) {
            $res = $app($req, $res);
            break;
        }
    }
    return $res;
};
