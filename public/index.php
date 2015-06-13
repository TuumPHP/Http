<?php

/**
 * give it a try.
 */
use Tuum\Http\RequestHelper;
use Tuum\Http\ResponseHelper;

include __DIR__."/classes/autoload.php";

$req = RequestHelper::createFromGlobal($GLOBALS);
$res = ResponseHelper::createResponse('<h1>Not Found</h1>');

/** @var Closure $app */
$app = include __DIR__.'/classes/app.php';
$res = $app($req, $res);
ResponseHelper::emit($res);