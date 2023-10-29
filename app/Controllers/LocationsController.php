<?php

namespace App\Controllers;

use App\Api\LocationsApiClient;
use App\Core\ApiServiceContainer;
use App\Core\Renderer;
use GuzzleHttp\Psr7\ServerRequest;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class LocationsController
{
    private const PATH = 'location/?';

    private Renderer $renderer;
    private LocationsApiClient $locationsApiClient;
    private ServerRequest $request;
    private Response $response;
    private ApiServiceContainer $serviceContainer;
    private array $vars;

    public function __construct(ServerRequest $request, $vars)
    {
        $this->vars = $vars;
        $this->request = $request;
        $this->renderer = new Renderer();
        $this->response = new Response();
        $this->serviceContainer = new ApiServiceContainer();
        $this->locationsApiClient = $this->serviceContainer->getLocationApiClient();
    }

    public function locations(array $filterQuery = []): ResponseInterface
    {
        if (isset($this->vars['id'])) {
            $this->getSingleLocation();
        } else {
            $queryParams = $this->request->getQueryParams();
            $queryShadow = empty($filterQuery) ? $queryParams : $filterQuery;
            $filterQuery = empty($filterQuery) ? $queryParams : $filterQuery;
            unset($queryShadow['page']);

            $queryShadow = http_build_query($queryShadow);
            $queryShadow = $queryShadow ? '&' . $queryShadow : '';

            $methodName = __METHOD__;
            $pageName = substr(strrchr($methodName, '::'), 1);
            $page = $queryParams['page'] ?? 1;

            $query = http_build_query($filterQuery);
            $uri = self::PATH . $query;

            $content = $this->locationsApiClient->getLocations($uri);
            $html = $this->renderer->renderPage('Locations/Locations.twig', $content, $pageName, $page, $queryShadow);
            $this->response->getBody()->write($html);
        }
        return $this->response->withHeader('Content-Type', 'text/html');
    }

    public function filter(): ResponseInterface
    {
        $queryParameters = [];
        foreach (['name', 'type', 'dimension'] as $param) {
            if (!empty($_POST[$param])) {
                $queryParameters[$param] = $_POST[$param];
            }
        }
        return $this->locations($queryParameters);
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
