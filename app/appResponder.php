<?php

use App\App\Dispatcher;
use Tuum\Respond\Helper\ResponderBuilder;
use Tuum\Respond\Responder;
use Tuum\Respond\Responder\Error;
use Tuum\Respond\Service\Renderer\Plates;
use Tuum\Respond\Service\Renderer\Tuum;
use Tuum\Respond\Service\Renderer\Twig;
use Tuum\Respond\Service\SessionStorage;

/**
 * @param array      $config
 * @param Dispatcher $app
 * @return Responder
 */
return function (array $config, Dispatcher $app) {

    /**
     * this is the view for template.
     */
    if ($config['view'] === 'twig') {
        $viewer = Twig::forge(__DIR__ . '/twigs');
    } elseif ($config['view'] === 'plates') {
        $viewer = Plates::forge(__DIR__ . '/plates');
    } else {
        $viewer = Tuum::forge(__DIR__ . '/views');
    }

    /**
     * construct responder.
     */
    $session   = SessionStorage::forge('sample');
    $responder = ResponderBuilder::withView(
        $viewer,
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