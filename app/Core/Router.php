<?php

namespace App\Core;

use FastRoute;
use FastRoute\Dispatcher;
use GuzzleHttp\Psr7\ServerRequest;
use Nyholm\Psr7\Factory\Psr17Factory;


use Psr\Http\Message\ResponseInterface;

class Router
{
    private Dispatcher $dispatcher;

    public function __construct()
    {
        $this->dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
            $r->addRoute(['GET'], '/', 'HomeController@home');

            $r->addRoute(['GET'], '/characters[/{page:\d+}]', 'CharactersController@characters');
            $r->addRoute(['GET'], '/character[/{id:\d+}]', 'CharactersController@characters');

            $r->addRoute(['GET'], '/episodes[/{page:\d+}]', 'EpisodesController@episodes');
            $r->addRoute(['GET'], '/episode[/{id:\d+}]', 'EpisodesController@episodes');

            $r->addRoute(['GET'], '/locations[/{page:\d+}]', 'LocationsController@locations');
            $r->addRoute(['GET'], '/location[/{page:\d+}]', 'LocationsController@singleLocation');
            $r->addRoute(['GET'], '/searchPage[/{page:\d+}]', 'SearchController@searchPage');
            $r->addRoute(['POST'], '/searchResults[/{page:\d+}]', 'SearchController@searchResults');
        });
    }

    public function route(): ResponseInterface
    {
        $responseFactory = new Psr17Factory();
        $request = ServerRequest::fromGlobals();
        $routeInfo = $this->dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                return $responseFactory->createResponse(404);
            case Dispatcher::METHOD_NOT_ALLOWED:
                return $responseFactory->createResponse(405);
            case Dispatcher::FOUND:

                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                [$controllerName, $methodName] = explode('@', $handler);
                $controllerClass = 'App\\Controllers\\' . $controllerName;
                $controller = new $controllerClass();

                return $controller->{$methodName}($vars);
        }
    }
}
