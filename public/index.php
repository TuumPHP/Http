<?php

/**
 * a sample web application using Tuum/Respond.
 */
use Tuum\Respond\Helper\ReqBuilder;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\SapiEmitter;

/** @var Closure $app */

include dirname(__DIR__) . "/app/autoload.php";

/**
 * run web application for the request.
 */
$app = include dirname(__DIR__) . '/app/app.php';
$req = ReqBuilder::createFromGlobal($GLOBALS);
$res = new Response();
$res = $app($req, $res);

$emitter = new SapiEmitter;
$emitter->emit($res);