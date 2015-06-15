<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Http\RequestHelper;
use Tuum\Http\Service\SessionStorageInterface;
use Tuum\Http\Service\ViewStream;
use Tuum\Http\Service\ViewStreamInterface;

/**
 * @param ServerRequestInterface $req
 * @param ResponseInterface      $res
 * @return ResponseInterface
 */
return function($req, $res) {
    
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
     * create a container and set it to the $req.
     */
    $app = new Container();
    $view = ViewStream::forge(__DIR__.'/views');
    $app->set(ViewStreamInterface::class, $view);
    $req = RequestHelper::withApp($req, $app);

    /** 
     * run the router!!!
     * 
     * @var Closure $router
     */
    $router = include __DIR__ . "/routes.php";;
    $res    = $router($req, $res);
    
    /**
     * done. save session. 
     */
    $session->commit();
    return $res;
};
