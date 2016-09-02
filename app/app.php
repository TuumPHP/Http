<?php

use App\App\Dispatcher;
use PhpMiddleware\PhpDebugBar\PhpDebugBarMiddlewareFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder;
use Zend\Diactoros\Response;

$builder = function($config) {

    /** @var Dispatcher $app */
    $app = new Dispatcher();

    $provider = new \App\App\Provider($config);
    $provider->load($app->getContainer());

    /** @var callable $router */
    $router = include __DIR__ . '/appRoutes.php';
    $app    = $router($app);

    return $app;
};

/** @var Dispatcher $app */
$config = isset($config) ? $config : [];
$app = $builder($config);

/**
 * creates services.
 *
 * @param ServerRequestInterface $request
 * @param ResponseInterface      $response
 * @return ResponseInterface
 */
$next = function (ServerRequestInterface $request, ResponseInterface $response) use($app) {
    $request = Respond::withResponder($request, $app->get(Responder::class));
    return $app->run($request, $response);
};

return function (ServerRequestInterface $request, ResponseInterface $response) use($next) {

    $factory  = new PhpDebugBarMiddlewareFactory();
    $middle   = $factory();
    $response = $middle($request, $response, $next);
    return $response;

};
