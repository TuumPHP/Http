<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\RequestHelper;
use Tuum\Respond\Service\SessionStorage;

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
    $session = SessionStorage::forge('sample');
    $req     = RequestHelper::withSessionMgr($req, $session);

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