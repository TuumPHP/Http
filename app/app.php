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
    $container = new \App\App\Container([]);
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

/**
 * runs the application.
 *
 * @param ServerRequestInterface $request
 * @param ResponseInterface      $response
 * @return ResponseInterface
 */
$next = function (ServerRequestInterface $request, ResponseInterface $response) use($app) {
    Respond::setResponder($app->get(Responder::class));
    return $app->run($request, $response);
};

/**
 * checks for CSRF token as forbidden errors.
 *
 * @param ServerRequestInterface $request
 * @param ResponseInterface      $response
 * @return mixed
 */
$forbidden = new CsRfCheck($app->get(Responder::class), $next);

/**
 * adds DebugBar middleware.
 *
 * @param ServerRequestInterface $request
 * @param ResponseInterface      $response
 * @return ResponseInterface
 */
return function (ServerRequestInterface $request, ResponseInterface $response) use($forbidden) {

    $factory  = new PhpDebugBarMiddlewareFactory();
    $middle   = $factory();
    $response = $middle($request, $response, $forbidden);
    return $response;

};
