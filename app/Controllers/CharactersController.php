<?php

namespace App\Controllers;

use App\API\CharactersRequest;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use App\Core\Renderer;

class CharactersController
{
    private Renderer $renderer;
    private CharactersRequest $charactersRequest;
    private Response $response;

    public function __construct()
    {
        $this->renderer = new Renderer();
        $this->response = new Response();
        $this->charactersRequest = new CharactersRequest();
    }

    public function characters($vars): ResponseInterface
    {
        if (isset($vars['id'])) {
            $id = $vars['id'];
            $uri = "character/{$id}";
            $content = $this->charactersRequest->getSingleCharacter($uri);
            $html = $this->renderer->renderSinglePage('SingleCharacter.twig', $content);
            $this->response->getBody()->write($html);
        } else {
            $page = $vars['page'] ?? 1;
            $uri = "character?page={$page}";
            $content = $this->charactersRequest->getCharacters($uri);
            $html = $this->renderer->renderPage('Characters.twig', $content);
            $this->response->getBody()->write($html);
        }
        return $this->response->withHeader('Content-Type', 'text/html');
    }
}

