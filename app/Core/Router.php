<?php

namespace App\Core;

use App\Controllers\ErrorsController;
use FastRoute;
use FastRoute\Dispatcher;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;

class Router
{
    private Dispatcher $dispatcher;

    public function __construct()
    {
        $this->dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
            $r->addRoute(['GET'], '/', 'CharactersController@home');
            $r->addRoute(['GET'], '/characters/home', 'CharactersController@home');
            $r->addRoute(['GET'], '/characters[/{page:\d+}]', 'CharactersController@characters');
            $r->addRoute(['POST'], '/characters/filter', 'CharactersController@filter');
            $r->addRoute(['GET'], '/character[/{id:\d+}]', 'CharactersController@characters');

            $r->addRoute(['GET'], '/episodes/home', 'EpisodesController@home');
            $r->addRoute(['GET'], '/episodes[/{page:\d+}]', 'EpisodesController@episodes');
            $r->addRoute(['POST'], '/episodes/filter', 'EpisodesController@filter');
            $r->addRoute(['GET'], '/episode[/{id:\d+}]', 'EpisodesController@episodes');

            $r->addRoute(['GET'], '/locations/home', 'LocationsController@home');
            $r->addRoute(['GET'], '/locations[/{page:\d+}]', 'LocationsController@locations');
            $r->addRoute(['POST'], '/locations/filter', 'LocationsController@filter');
            $r->addRoute(['GET'], '/location[/{id:\d+}]', 'LocationsController@locations');
        });
    }

    public function route(): ResponseInterface
    {
        $request = ServerRequest::fromGlobals();
        $routeInfo = $this->dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());
        $request = ServerRequest::fromGlobals();

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                $errorsController = new ErrorsController(404);
                return $errorsController->error();
            case Dispatcher::METHOD_NOT_ALLOWED:
                $errorsController = new ErrorsController(405);
                return $errorsController->error();
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                $vars['page'] = $vars['page'] ?? 1;
                [$controllerName, $methodName] = explode('@', $handler);
                $controllerClass = 'App\\Controllers\\' . $controllerName;

                if (class_exists($controllerClass)) {
                    $controller = new $controllerClass($request,$vars);
                    if (method_exists($controller, $methodName)) {
                        return $controller->{$methodName}();
                    }
                }
        }

        $errorsController = new ErrorsController(500);
        return $errorsController->error();
    }
}
