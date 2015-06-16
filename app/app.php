<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\RequestHelper;
use Tuum\Respond\Responder\Error;
use Tuum\Respond\Service\ErrorView;
use Tuum\Respond\Service\ErrorViewInterface;
use Tuum\Respond\Service\ViewStream;
use Tuum\Respond\Service\ViewStreamInterface;

/** @var Closure $next */
$next = include __DIR__ . '/appSession.php';

/**
 * creates services.
 *
 * @param ServerRequestInterface $req
 * @return ResponseInterface
 */
return function($req) use($next) {

    /**
     * this is the container/app.
     */
    $app = new Container();

    /**
     * this is the view for template.
     */
    $view = ViewStream::forge(__DIR__.'/views');
    $app->set(ViewStreamInterface::class, $view);

    /**
     * this is the view for error.
     */
    $error = new ErrorView($view);
    $error->default_error = 'errors/error';
    $error->statusView = [
        Error::FILE_NOT_FOUND => 'errors/notFound',
    ];
    $app->set(ErrorViewInterface::class, $error);
    set_exception_handler($error); // catch uncaught exception!!!

    /**
     * done constructing services. dispatch next.
     */
    $req = RequestHelper::withApp($req, $app);
    return $next($req);
};

