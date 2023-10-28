<?php

namespace App\Controllers;

use App\Api\CharactersApiClient;
use App\Core\ApiServiceContainer;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use App\Core\Renderer;

class CharactersController
{
    private Renderer $renderer;
    private Response $response;
    private ApiServiceContainer $serviceContainer;
    private CharactersApiClient $charactersApiClient;

    public function __construct()
    {
        $this->renderer = new Renderer();
        $this->response = new Response();
        $this->serviceContainer = new ApiServiceContainer();
        $this->charactersApiClient = $this->serviceContainer->getCharacterApiClient();
    }

    public function characters(array $vars): ResponseInterface
    {
        if (isset($vars['id'])) {
            $id = $vars['id'];
            $uri = "character/{$id}";
            $content = $this->charactersApiClient->getSingleCharacter($uri);
            $html = $this->renderer->renderSinglePage('Characters/SingleCharacter.twig', $content);
            $this->response->getBody()->write($html);
        } else {
            $methodName = __METHOD__;
            $pageName = substr(strrchr($methodName, '::'), 1);
            $page = $vars['page'] ?? 1;
            $uri = "character?page={$page}";
            $content = $this->charactersApiClient->getCharacters($uri);
            $html = $this->renderer->renderPage('Characters/Characters.twig', $content, $pageName, $page);
            $this->response->getBody()->write($html);
        }
        return $this->response->withHeader('Content-Type', 'text/html');
    }

    public function filter()
    {
        var_dump($_POST);
    }
}

