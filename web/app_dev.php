<?php

use MichaelR\SfMicro\MicroKernel;
use Symfony\Component\HttpFoundation\Request;

$loader = require dirname(__DIR__) . '/vendor/autoload.php';

$request = Request::createFromGlobals();
$app = new MicroKernel('dev', true);
$app->loadClassCache();
$response = $app->handle($request);

$response->send();

$app->terminate($request, $response);
