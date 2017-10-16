<?php

use App\App\CsRfCheck;
use App\App\Dispatcher;
use PhpMiddleware\PhpDebugBar\PhpDebugBarMiddlewareFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder;

/**
 * builds an application.
 *
 * @param array $config
 * @return Dispatcher
 */
$builder = function($config) {

    $provider  = new \App\App\Provider($config);
    $container = new \App\App\Container($config);
    $container->loadServices($provider);
    $app = new Dispatcher($container);
    
    /** @var callable $router */
    $router = include __DIR__ . '/appRoutes.php';
    $app    = $router($app);

    return $app;
};

/** @var Dispatcher $app */
$config = isset($config) ? $config : [];
$app = $builder($config);

return $app;
