<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder;
use Tuum\Respond\Responder\Error;
use Tuum\Respond\Service\ErrorView;
use Tuum\Respond\Service\SessionStorage;
use Tuum\Respond\Service\TwigStream;
use Tuum\Respond\Service\ViewStream;

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
    if ($request->getAttribute('view') === 'twig') {
        $view = TwigStream::forge(__DIR__ . '/twigs');
        /**
         * this is the view for error.
         */
        $error = ErrorView::forge($view, [
            'default' => 'errors/error',
            'status'  => [
                Error::FILE_NOT_FOUND => 'errors/notFound',
            ],
            'handler' => true,
        ]);

    } else {
        $view = ViewStream::forge(__DIR__ . '/views');
        /**
         * this is the view for error.
         */
        $error = ErrorView::forge($view, [
            'default' => 'errors/error',
            'status'  => [
                Error::FILE_NOT_FOUND => 'errors/notFound',
            ],
            'handler' => true,
        ]);

    }

    /**
     * this is the session.
     */
    $session   = SessionStorage::forge('sample');
    $responder = Responder::build($view, $error, 'contents')->withSession($session);
    $request   = $request->withAttribute(Responder::class, $responder);

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

