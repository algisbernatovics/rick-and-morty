<?php

namespace App\Controllers;

use App\Api\EpisodesApiClient;
use App\Core\ApiServiceContainer;
use App\Core\Renderer;
use GuzzleHttp\Psr7\ServerRequest;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class EpisodesController
{
    private const PATH = 'episode/?';
    private const PAGENAME = 'episodes';
    private Renderer $renderer;
    private EpisodesApiClient $episodesApiClient;
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
        $this->episodesApiClient = $this->serviceContainer->getEpisodesRequest();
    }

    public function episodes(array $filterQuery = []): ResponseInterface
    {
        if (isset($this->vars['id'])) {
            $this->getSingleEpisode();
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

            $content = $this->episodesApiClient->getEpisodes($uri);
            $html = $this->renderer->renderPage('Episodes/Episodes.twig', $content, self::PAGENAME, $page, $queryShadow);
            $this->response->getBody()->write($html);
        }
        return $this->response->withHeader('Content-Type', 'text/html');
    }

    public function filter(): ResponseInterface
    {
        $parsedBody = $this->request->getParsedBody();

        $queryParameters = [];
        foreach (['name', 'episode'] as $param) {
            if (isset($parsedBody[$param]) && !empty($parsedBody[$param])) {
                $queryParameters[$param] = $parsedBody[$param];
            }
        }

        return $this->episodes($queryParameters);
    }



    private function getSingleEpisode(): void
    {
        $id = $this->vars['id'];
        $uri = "episode/{$id}";
        $content = $this->episodesApiClient->getSingleEpisode($uri);
        $html = $this->renderer->renderSinglePage('Episodes/SingleEpisode.twig', $content,self::PAGENAME);
        $this->response->getBody()->write($html);
    }
}
