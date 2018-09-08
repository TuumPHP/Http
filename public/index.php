<?php

/**
 * a sample web application using Tuum/Respond.
 */
use Tuum\Respond\Helper\ReqBuilder;
use Tuum\Respond\Service\Renderer\Plates;
use Zend\Diactoros\Response\SapiEmitter;

if (php_sapi_name() == 'cli-server') {
    $file = __DIR__ . '/' . $_SERVER["REQUEST_URI"];
    if (file_exists($file) && is_file($file)) {
        return false;
    }
}

/** @var Closure $app */

include dirname(__DIR__) . "/app/autoload.php";
include dirname(__DIR__) . "/app/boot-demo.php";

/**
 * run web application for the request.
 */

/** @var \App\App\Dispatcher $app */
$app = bootDemo([
    'template_dir' => dirname(__DIR__). '/app/plates',
    'content_view' => 'layouts/contents',
    'renderer_type' => Plates::class,
]);
$req = ReqBuilder::createFromGlobal($GLOBALS);
$res = $app->run($req);

$emitter = new SapiEmitter;
$emitter->emit($res);