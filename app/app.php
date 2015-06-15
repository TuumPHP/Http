<?php

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Http\RequestHelper;
use Tuum\Http\Respond;
use Tuum\Http\Responder\Error;
use Tuum\Http\Service\ErrorView;
use Tuum\Http\Service\ErrorViewInterface;
use Tuum\Http\Service\SessionStorageInterface;
use Tuum\Http\Service\ViewStream;
use Tuum\Http\Service\ViewStreamInterface;

/**
 * creates a container with necessary services.
 *
 * @return Container|ContainerInterface
 */
$getApp = function() {

    // this is the container/app.
    $app = new Container();

    // this is the view for template.
    $view = ViewStream::forge(__DIR__.'/views');
    $app->set(ViewStreamInterface::class, $view);

    // this is the view for error.
    $error = new ErrorView($view);
    $error->default_error = 'errors/error';
    $error->statusView = [
        Error::FILE_NOT_FOUND => 'errors/notFound',
    ];
    $app->set(ErrorViewInterface::class, $error);
    set_exception_handler($error); // catch uncaught exception!!!

    // done.
    return $app;
};

/**
 * the main application!
 *
 * @param ServerRequestInterface $req
 * @return ResponseInterface
 */
return function($req) use($getApp) {
    
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
    $app = $getApp();
    $req = RequestHelper::withApp($req, $app);

    /** 
     * run the router!!!
     * 
     * @var Closure $router
     */
    $router = include __DIR__ . "/routes.php";
    $res    = Respond::error($req)->notFound();
    $res    = $router($req, $res);
    
    /**
     * done. save session. 
     */
    $session->commit();
    return $res;
};
