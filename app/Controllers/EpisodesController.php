<?php

namespace App\Controllers;

use App\API\EpisodesRequest;
use App\Core\Renderer;
use Couchbase\ValueRecorder;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class EpisodesController
{
    private Renderer $renderer;
    private EpisodesRequest $episodesRequest;
    private Response $response;

    public function __construct()
    {
        $this->renderer = new Renderer();
        $this->response = new Response();
        $this->episodesRequest = new EpisodesRequest();
    }

    public function episodes($vars): ResponseInterface
    {
        if (isset($vars['id'])) {
            $id = $vars['id'];
            $uri = "episode/{$id}";
            $content = $this->episodesRequest->getSingleEpisode($uri);
            $html = $this->renderer->renderSinglePage('SingleEpisode.twig', $content);
            $this->response->getBody()->write($html);
        } else {
            $page = $vars['page'] ?? 1;
            $uri = "episode?page={$page}";
            $content = $this->episodesRequest->getEpisodes($uri);
            $html = $this->renderer->renderPage('Episodes.twig', $content);
            $this->response->getBody()->write($html);
        }
        return $this->response->withHeader('Content-Type', 'text/html');
    }
}