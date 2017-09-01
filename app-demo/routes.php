<?php

use Demo\Controller\DocumentMap;
use Demo\Controller\JumpController;
use Demo\Controller\PaginationController;
use Demo\Controller\UploadController;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

/** @var App $app */

// Routes
$app->get('/', function (ServerRequestInterface $request, $response, $args) {
    return $this->responder->view($request, $response)->render('index', $args);
});

$app->get('/throw', function () {
    throw new \BadMethodCallException('always throws an exception');
});

/**
 * jump and jumper to see the redirection and parameter in flash
 */
$app->any('/jump', JumpController::class);
$app->any('/upload', UploadController::class);
$app->any('/paginate', PaginationController::class);

/**
 * document map
 */
$app->any('/docs/{contents}', DocumentMap::class);
$app->any('/docs/', DocumentMap::class);
$app->any('/docs', DocumentMap::class);
