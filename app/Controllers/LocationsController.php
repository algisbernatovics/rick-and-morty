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
    private array $vars;

    public function __construct($vars)
    {
        $this->vars = $vars;
        $this->renderer = new Renderer();
        $this->response = new Response();
        $this->serviceContainer = new ApiServiceContainer();
        $this->locationsApiClient = $this->serviceContainer->getLocationApiClient();
    }

    public function locations(): ResponseInterface
    {
        if (isset($this->vars['id'])) {
            $this->getSingleLocation();
        } else {
            $methodName = __METHOD__;
            $pageName = substr(strrchr($methodName, '::'), 1);
            $uri = "location?page={$this->vars['page']}";
            $content = $this->locationsApiClient->getLocations($uri);
            $html = $this->renderer->renderPage('Locations/Locations.twig', $content, $pageName, $this->vars['page']);
            $this->response->getBody()->write($html);
        }
        return $this->response->withHeader('Content-Type', 'text/html');
    }

    private function getSingleLocation(): void
    {
        $id = $this->vars['id'];
        $uri = "location/{$id}";
        $content = $this->locationsApiClient->getSingleLocation($uri);
        $html = $this->renderer->renderSinglePage('Locations/SingleLocation.twig', $content);
        $this->response->getBody()->write($html);
    }
}
