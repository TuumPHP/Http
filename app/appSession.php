<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\RequestHelper;
use Tuum\Respond\Service\SessionStorageInterface;

/** @var Closure $next */
$next = include __DIR__ . '/appRoutes.php';


/**
 * set up sessions.
 *
 * @param ServerRequestInterface $req
 * @return ResponseInterface
 */
return function($req) use($next) {

    /**
     * create a session and set it to the $req.
     */
    $factory = new \Aura\Session\SessionFactory();
    $session = $factory->newInstance($_COOKIE);
    $session->start();
    $segment = $session->getSegment('sample');
    /** @var SessionStorageInterface $segment */
    $req     = RequestHelper::withSessionMgr($req, $segment);

    /**
     * run the router!!!
     *
     * @var Closure $router
     */
    $res    = $next($req);

    /**
     * done. save session.
     */
    $session->commit();
    return $res;
};