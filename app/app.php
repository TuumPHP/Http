<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder\Error;
use Tuum\Respond\Service\ErrorView;
use Tuum\Respond\Service\ErrorViewInterface;
use Tuum\Respond\Service\SessionStorage;
use Tuum\Respond\Service\SessionStorageInterface;
use Tuum\Respond\Service\ViewStream;
use Tuum\Respond\Service\ViewStreamInterface;

/** @var Closure $next */
$next = include __DIR__ . '/appRoutes.php';

/**
 * creates services.
 *
 * @param ServerRequestInterface $request
 * @return ResponseInterface
 */
return function (ServerRequestInterface $request) use ($next) {

    /**
     * this is the view for template.
     */
    $view    = ViewStream::forge(__DIR__ . '/views');
    $request = $request->withAttribute(ViewStreamInterface::class, $view);

    /**
     * this is the view for error.
     */
    $error                = new ErrorView($view);
    $error->default_error = 'errors/error';
    $error->statusView    = [
        Error::FILE_NOT_FOUND => 'errors/notFound',
    ];
    set_exception_handler($error); // catch uncaught exception!!!
    $request = $request->withAttribute(ErrorViewInterface::class, $error);

    /**
     * this is the session.
     */
    $session = SessionStorage::forge('sample');
    $request = $request->withAttribute(SessionStorageInterface::class, $session);

    /**
     * run the next process!!!
     */
    $response = $next($request) ?: Respond::error($request)->notFound();

    /**
     * done. save session.
     */
    $session->commit();
    return $response;
};

