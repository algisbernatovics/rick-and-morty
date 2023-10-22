<?php

namespace App\Controllers;

use App\ClientRequest;
use GuzzleHttp\Psr7\ServerRequest;
use http\Env\Request;
use Nyholm\Psr7\Response;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Core\Renderer;

class CharacterController
{
    private ServerRequest $request;
    private Renderer $renderer;
    private ClientRequest $clientRequest;
    private Response $response;

    public function __construct(ServerRequest $request)
    {
        $this->request = $request;
        $this->renderer = new Renderer();
        $this->response = new Response();
        $this->clientRequest = new ClientRequest();
    }

    public function characters($vars): ResponseInterface
    {
        if (isset($vars['page'])) {
            $page = $vars['page'];
            $uri = "character?page={$page}";
            $content = $this->clientRequest->getCharacters($uri);
            $html = $this->renderer->renderPage('Characters.twig', $content);
            $this->response->getBody()->write($html);
        } else {
            $id = $vars['id'];
            $uri = "character/{$id}";
            $content = $this->clientRequest->getSingleCharacter($uri);
            $html = $this->renderer->renderSinglePage('SingleCharacter.twig', $content);
            $this->response->getBody()->write($html);
        }

        return $this->response->withHeader('Content-Type', 'text/html');
    }
}

