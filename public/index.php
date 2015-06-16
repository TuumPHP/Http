<?php

/**
 * give it a try.
 */
use Psr\Http\Message\ResponseInterface;
use Tuum\Respond\RequestHelper;
use Tuum\Respond\ResponseHelper;

/** @var ResponseInterface $res */
/** @var Closure $app */

include dirname(__DIR__) . "/app/autoload.php";

/**
 * run web application for the request.
 */
$app = include dirname(__DIR__) . '/app/app.php';
$req = RequestHelper::createFromGlobal($GLOBALS);
$res = $app($req);

ResponseHelper::emit($res);