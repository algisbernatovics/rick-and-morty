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
    private const PAGENAME = 'locations';
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

    public function locations(array $filterQuery = [],bool $ajax = true): ResponseInterface
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

            $page = $queryParams['page'] ?? 1;

            $query = http_build_query($filterQuery);
            $uri = self::PATH . $query;

            $content = $this->locationsApiClient->getLocations($uri);

            if ($ajax){
                $html = $this->renderer->renderPage('Locations/List.twig', $content, self::PAGENAME, $page, $queryShadow);
            }else{
                $html = $this->renderer->renderPage('Locations/Locations.twig', $content, self::PAGENAME, $page, $queryShadow);
            }

            $this->response->getBody()->write($html);
        }
        return $this->response->withHeader('Content-Type', 'text/html');
    }
    public function home()
    {
        return $this->locations([self::PATH],0);
    }
    public function filter(): ResponseInterface
    {
        $parsedBody = $this->request->getParsedBody();

        $queryParameters = [];
        foreach (['name', 'type', 'dimension'] as $param) {
            if (isset($parsedBody[$param]) && !empty($parsedBody[$param])) {
                $queryParameters[$param] = $parsedBody[$param];
            }
        }

        return $this->locations($queryParameters);
    }

    private function getSingleLocation(): void
    {
        $id = $this->vars['id'];
        $uri = "location/{$id}";
        $content = $this->locationsApiClient->getSingleLocation($uri);
        $html = $this->renderer->renderSinglePage('Locations/SingleLocation.twig', $content,self::PAGENAME);
        $this->response->getBody()->write($html);
    }
}
