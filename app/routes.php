<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Http\RequestHelper;
use Tuum\Http\Respond;

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
        ->asHtml('
<h1>Jump!!</h1>
<a href="jumper"/>jump!</a>
<p></p>
');
};

/**
 * @param ServerRequestInterface $req
 * @param ResponseInterface      $res
 * @return ResponseInterface
 */
$jumper = function($req, $res) {
    return Respond::redirect($req, $res)
        ->toPath('jump');
};

/**
 * @param ServerRequestInterface $req
 * @param ResponseInterface      $res
 * @return ResponseInterface
 */
$throw = function($req, $res) {
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
