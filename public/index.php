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

$req  = RequestHelper::createFromGlobal($GLOBALS);
$app = include dirname(__DIR__) . '/app/app.php';
$res = $app($req);

ResponseHelper::emit($res);