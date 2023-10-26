<?php

require '../vendor/autoload.php';

use App\Core\Router;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/../.env');

// Create a PSR-17 factory for responses
$responseFactory = new Psr17Factory();

// Create a ServerRequestCreator
$serverRequestCreator = new ServerRequestCreator(
    $responseFactory, // ServerRequest factory
    $responseFactory, // Uri factory
    $responseFactory, // UploadedFile factory
    $responseFactory  // Stream factory
);

// Create a ServerRequest using ServerRequestCreator
$serverRequest = $serverRequestCreator->fromGlobals();

// Define your routes and handle the request
$router = new Router();
$response = $router->route($serverRequest);

// Output HTTP response
foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header($name . ': ' . $value, false);
    }
}

http_response_code($response->getStatusCode());
echo $response->getBody();
