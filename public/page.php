<?php
/**
 * a sample for page-controller using Tuum/Respond.
 *
 * This is for legacy site.
 */
use Tuum\Respond\Responder;

include dirname(__DIR__) . "/app/autoload.php";
include __DIR__ . "/pages/PageController.php";
include __DIR__ . '/pages/Request.php';

$controller = PageController::forge();
$request    = RequestBuilder::forge();
$response   = $controller->invoke($request);
if ($response) {
    echo $response->getBody()->__toString();
}