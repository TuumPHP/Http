<?php

use App\App\Dispatcher;
use DebugBar\StandardDebugBar;
use PhpMiddleware\PhpDebugBar\PhpDebugBarMiddlewareFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder;
use Zend\Diactoros\Response;

/**
 * creates services.
 *
 * @param ServerRequestInterface $request
 * @param ResponseInterface      $response
 * @return ResponseInterface
 */
$next = function (ServerRequestInterface $request, ResponseInterface $response) {


    /** @var callable $responderBuilder */
    $responderBuilder = include __DIR__ . '/appResponder.php';

    /** @var Responder $responder */
    $responder = $responderBuilder($request, $response);
    $request   = Respond::withResponder($request, $responder);

    /** @var callable $appBuilder */
    $appBuilder = include __DIR__ . '/appBuilder.php';

    /** @var Dispatcher $app */
    $app = $appBuilder($responder);

    /**
     * run the next process!!!
     */
    try {

        $response = $app->run($request, $response) ?: $responder->error($request, $response)->notFound();

    } catch (\Exception $e) {
        $response = $responder->error($request, $response)->asView(500);
    }

    return $response;
};

return function (ServerRequestInterface $request, ResponseInterface $response) use($next) {

    $factory  = new PhpDebugBarMiddlewareFactory();
    $middle   = $factory();
    $response = $middle($request, $response, $next);
    return $response;

};
