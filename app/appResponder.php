<?php

use App\App\Dispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Helper\ResponderBuilder;
use Tuum\Respond\Responder;
use Tuum\Respond\Responder\Error;
use Tuum\Respond\Service\ErrorView;
use Tuum\Respond\Service\SessionStorage;
use Tuum\Respond\Service\TuumViewer;
use Tuum\Respond\Service\TwigViewer;

/**
 * @param ServerRequestInterface $request
 * @param ResponseInterface      $response
 * @param Dispatcher             $app
 * @return Responder
 */
return function (ServerRequestInterface $request, ResponseInterface $response, Dispatcher $app) {

    /**
     * this is the view for template.
     */
    if ($request->getAttribute('view') === 'twig') {
        $view = TwigViewer::forge(__DIR__ . '/twigs');
    } else {
        $view = TuumViewer::forge(__DIR__ . '/views');
    }

    /**
     * construct responder.
     */
    $session   = SessionStorage::forge('sample');
    $error     = ErrorView::forge($view, [
        'default' => 'errors/error',
        'status'  => [
            Error::FILE_NOT_FOUND => 'errors/notFound',
        ],
    ]);
    $responder = ResponderBuilder::withServices(
        $view, 
        $error, 
        'layouts/contents',
        $app->getResolver())
        ->withResponse($response)
        ->withSession($session);

    return $responder;
};