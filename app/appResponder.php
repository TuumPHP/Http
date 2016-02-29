<?php

use App\App\Dispatcher;
use Tuum\Respond\Helper\ResponderBuilder;
use Tuum\Respond\Responder;
use Tuum\Respond\Responder\Error;
use Tuum\Respond\Service\SessionStorage;
use Tuum\Respond\Service\TuumViewer;
use Tuum\Respond\Service\TwigViewer;

/**
 * @param Dispatcher             $app
 * @return Responder
 */
return function (array $config, Dispatcher $app) {

    /**
     * this is the view for template.
     */
    if ($config['view'] === 'twig') {
        $view = TwigViewer::forge(__DIR__ . '/twigs');
    } else {
        $view = TuumViewer::forge(__DIR__ . '/views');
    }

    /**
     * construct responder.
     */
    $session   = SessionStorage::forge('sample');
    $responder = ResponderBuilder::withView(
        $view,
        [
            'default' => 'errors/error',
            'status'  => [
                Error::FILE_NOT_FOUND => 'errors/notFound',
            ],
        ], 
        'layouts/contents',
        $app->getResolver()
    )->withSession($session);

    return $responder;
};