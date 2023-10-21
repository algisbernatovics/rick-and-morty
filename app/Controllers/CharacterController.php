<?php

namespace App\Controllers;

use App\ClientRequest;
use GuzzleHttp\Psr7\ServerRequest;
use http\Env\Request;
use Nyholm\Psr7\Response;
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
        $this->clientRequest = new ClientRequest($request->getRequestTarget());
    }

    public function character(): ResponseInterface
    {

        $content = $this->clientRequest->getCharacters();
        $html = $this->renderer->renderPage('Characters.twig', $content);

        $this->response->getBody()->write($html);
        return $this->response->withHeader('Content-Type', 'text/html');
    }

}

