<?php

/**
 * give it a try.
 */
use Psr\Http\Message\ResponseInterface;
use Tuum\Http\RequestHelper;
use Tuum\Http\ResponseHelper;

/** @var ResponseInterface $res */

include dirname(__DIR__) . "/app/autoload.php";

$req = RequestHelper::createFromGlobal($GLOBALS);
$res = ResponseHelper::createResponse('<h1>Not Found</h1>');
$path = $req->getUri()->getPath();

/** @var Closure $app */
$app = include dirname(__DIR__) . '/app/app.php';
$res = $app($req, $res);

ResponseHelper::emit($res);