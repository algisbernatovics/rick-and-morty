<?php

namespace App\Controllers;

use App\API\EpisodesApiClient;
use App\Core\ServiceContainer;
use App\Core\Renderer;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class EpisodesController
{
    private Renderer $renderer;
    private EpisodesApiClient $episodesApiClient;
    private Response $response;
    private ServiceContainer $serviceContainer;

    public function __construct()
    {
        $this->renderer = new Renderer();
        $this->response = new Response();
        $this->serviceContainer = new ServiceContainer();
        $this->episodesApiClient = $this->serviceContainer->getEpisodesRequest();
    }

    public function episodes($vars): ResponseInterface
    {
        if (isset($vars['id'])) {
            $id = $vars['id'];
            $uri = "episode/{$id}";
            $content = $this->episodesApiClient->getSingleEpisode($uri);
            $html = $this->renderer->renderSinglePage('SingleEpisode.twig', $content);
            $this->response->getBody()->write($html);
        } else {
            $page = $vars['page'] ?? 1;
            $uri = "episode?page={$page}";
            $content = $this->episodesApiClient->getEpisodes($uri);
            $html = $this->renderer->renderPage('Episodes.twig', $content);
            $this->response->getBody()->write($html);
        }
        return $this->response->withHeader('Content-Type', 'text/html');
    }
}
