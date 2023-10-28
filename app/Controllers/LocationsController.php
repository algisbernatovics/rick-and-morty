<?php

namespace App\Controllers;

use App\Api\LocationsApiClient;
use App\Core\Renderer;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use App\Core\ApiServiceContainer;

class LocationsController
{
    private Renderer $renderer;
    private LocationsApiClient $locationsApiClient;
    private Response $response;
    private ApiServiceContainer $serviceContainer;

    public function __construct()
    {
        $this->renderer = new Renderer();
        $this->response = new Response();
        $this->serviceContainer = new ApiServiceContainer();
        $this->locationsApiClient = $this->serviceContainer->getLocationApiClient();
    }

    public function locations(array $vars): ResponseInterface
    {
        if (isset($vars['id'])) {
            $id = $vars['id'];
            $uri = "location/{$id}";
            $content = $this->locationsApiClient->getSingleLocation($uri);
            $html = $this->renderer->renderSinglePage('Locations/SingleLocation.twig', $content);
            $this->response->getBody()->write($html);
        } else {
            $methodName = __METHOD__;
            $pageName = substr(strrchr($methodName, '::'), 1);
            $page = $vars['page'] ?? 1;
            $uri = "location?page={$page}";
            $content = $this->locationsApiClient->getLocations($uri);
            $html = $this->renderer->renderPage('Locations/Locations.twig', $content, $pageName, $page);
            $this->response->getBody()->write($html);
        }
        return $this->response->withHeader('Content-Type', 'text/html');
    }
}
