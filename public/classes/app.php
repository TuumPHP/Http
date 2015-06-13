<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Http\RequestHelper;
use Tuum\Http\Service\SessionStorageInterface;

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
    $segment = $session->getSegment('sample');
    /** @var SessionStorageInterface $segment */
    RequestHelper::withSessionMgr($req, $segment);

    /** 
     * run the router!
     * 
     * @var Closure $router
     */
    $router = include __DIR__."/routes.php";;
    $res    = $router($req, $res);
    
    /**
     * done. save session. 
     */
    $session->commit();
    return $res;
};
