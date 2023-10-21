<?php

require '../vendor/autoload.php';

use App\Core\Router;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

$router = new Router();

// Create a PSR-17 factory for responses
$responseFactory = new Psr17Factory();

// Create a ServerRequestCreator
$serverRequestCreator = new ServerRequestCreator(
    new Psr17Factory(), // ServerRequest factory
    new Psr17Factory(), // Uri factory
    new Psr17Factory(), // UploadedFile factory
    new Psr17Factory()  // Stream factory
);

// Create a ServerRequest using ServerRequestCreator
$serverRequest = $serverRequestCreator->fromGlobals();

$response = $router->route($serverRequest);

// Output HTTP response
foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header($name . ': ' . $value, false);
    }
}

http_response_code($response->getStatusCode());
echo $response->getBody();
