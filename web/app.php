<?php

use Symfony\Component\HttpFoundation\Request;

$loader = require dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__). '/app/MicroKernel.php';

$request = Request::createFromGlobals();
$app = new MicroKernel('prod', false);
$app->loadClassCache();
$response = $app->handle($request);

$response->send();

$app->terminate($request, $response);
