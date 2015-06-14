<?php

/**
 * give it a try.
 */
use Tuum\Http\RequestHelper;
use Tuum\Http\ResponseHelper;

include dirname(__DIR__) . "/app/autoload.php";

$req = RequestHelper::createFromGlobal($GLOBALS);
$res = ResponseHelper::createResponse('<h1>Not Found</h1>');

/** @var Closure $app */
$app = include dirname(__DIR__) . '/app/app.php';
$res = $app($req, $res);
ResponseHelper::emit($res);