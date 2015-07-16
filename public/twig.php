<?php

/**
 * a sample web application using Tuum/Respond.
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
$req = $req->withAttribute('view', 'twig');
$res = $app($req);

ResponseHelper::emit($res);