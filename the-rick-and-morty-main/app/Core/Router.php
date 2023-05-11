<?php

namespace App\Core;

use FastRoute;
use FastRoute\Dispatcher;


class Router
{
    public static function Router()
    {

        $dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {


            $r->addRoute(['GET'], '/', '\App\Controller\Controller@characters');
            $r->addRoute(['GET'], '/characters[/{page}]', '\App\Controller\Controller@characters');
            $r->addRoute(['GET'], '/locations[/{page}]', '\App\Controller\Controller@locations');
            $r->addRoute(['GET'], '/episodes[/{page}]', '\App\Controller\Controller@episodes');
            $r->addRoute(['GET'], '/character[/{page}]', '\App\Controller\Controller@character');
            $r->addRoute(['GET'], '/episode[/{page}]', '\App\Controller\Controller@episode');
            $r->addRoute(['GET'], '/location[/{page}]', '\App\Controller\Controller@location');
            $r->addRoute(['GET'], '/searchPage[/{page}]', '\App\Controller\Controller@searchPage');
            $r->addRoute(['POST'], '/searchResults[/{page}]', '\App\Controller\Controller@searchResults');

        });
        // Fetch method and URI from somewhere
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        // Strip query string (?foo=bar) and decode URI

        if (false !== $pos = strpos($uri, '?')) {

            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                // ... 404 Not Found
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                // ... 405 Method Not Allowed
                break;
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                [$controllerName, $methodName] = explode('@', $handler);
                $controller = new $controllerName;
                $response = $controller->{$methodName}((int)($vars['page']));
        }
        return $response;
    }
}