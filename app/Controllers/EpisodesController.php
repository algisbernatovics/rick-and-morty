<?php

namespace App\Controllers;

use App\Api\EpisodesApiClient;
use App\Core\ApiServiceContainer;
use App\Core\Renderer;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class EpisodesController
{
    private Renderer $renderer;
    private EpisodesApiClient $episodesApiClient;
    private Response $response;
    private ApiServiceContainer $serviceContainer;
    private array $vars;

    public function __construct(array $vars)
    {
        $this->vars = $vars;
        $this->renderer = new Renderer();
        $this->response = new Response();
        $this->serviceContainer = new ApiServiceContainer();
        $this->episodesApiClient = $this->serviceContainer->getEpisodesRequest();
    }

    public function episodes(): ResponseInterface
    {
        if (isset($this->vars['id'])) {
            $this->getSingleEpisode();
        } else {
            $methodName = __METHOD__;
            $pageName = substr(strrchr($methodName, '::'), 1);
            $uri = "episode?page={$this->vars['page']}";
            $content = $this->episodesApiClient->getEpisodes($uri);
            $html = $this->renderer->renderPage('Episodes/Episodes.twig', $content, $pageName, $this->vars['page']);
            $this->response->getBody()->write($html);
        }
        return $this->response->withHeader('Content-Type', 'text/html');
    }

    private function getSingleEpisode(): void
    {
        $id = $this->vars['id'];
        $uri = "episode/{$id}";
        $content = $this->episodesApiClient->getSingleEpisode($uri);
        $html = $this->renderer->renderSinglePage('Episodes/SingleEpisode.twig', $content);
        $this->response->getBody()->write($html);
    }
}
