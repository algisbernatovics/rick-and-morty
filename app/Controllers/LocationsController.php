<?php

namespace App\Controllers;

use App\API\LocationsRequest;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use App\Core\Renderer;

class LocationsController
{
    private Renderer $renderer;
    private LocationsRequest $locationsRequest;
    private Response $response;

    public function __construct()
    {
        $this->renderer = new Renderer();
        $this->response = new Response();
        $this->locationsRequest = new LocationsRequest();
    }

    public function locations($vars): ResponseInterface
    {
        if (isset($vars['id'])) {
            $id = $vars['id'];
            $uri = "location/{$id}";
            $content = $this->locationsRequest->getSingleLocation($uri);
            $html = $this->renderer->renderSinglePage('SingleLocation.twig', $content);
            $this->response->getBody()->write($html);
        } else {
            $page = $vars['page'] ?? 1;
            $uri = "location?page={$page}";
            $content = $this->locationsRequest->getLocations($uri);
            $html = $this->renderer->renderPage('Locations.twig', $content);
            $this->response->getBody()->write($html);
        }
        return $this->response->withHeader('Content-Type', 'text/html');
    }
}

