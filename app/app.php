<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\RequestHelper;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder;
use Tuum\Respond\Responder\Error;
use Tuum\Respond\Service\ErrorView;
use Tuum\Respond\Service\SessionStorage;
use Tuum\Respond\Service\TwigViewer;
use Tuum\Respond\Service\TuumViewer;
use Zend\Diactoros\Response;

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
        $view = TwigViewer::forge(__DIR__ . '/twigs');
    } else {
        $view = TuumViewer::forge(__DIR__ . '/views');
    }

    /**
     * construct session and responder.
     */
    $session   = SessionStorage::forge('sample');
    $error     = ErrorView::forge($view, [
        'default' => 'errors/error',
        'status'  => [
            Error::FILE_NOT_FOUND => 'errors/notFound',
        ],
        'handler' => true,
    ]);
    $responder = Responder::build($view, $error, 'layouts/contents')
        ->withSession($session)
        ->withResponse(new Response());
    $request   = RequestHelper::withSessionMgr($request, $session);
    $request   = RequestHelper::withResponder($request, $responder);

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

