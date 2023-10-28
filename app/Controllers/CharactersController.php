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
    private array $vars;

    public function __construct($vars)
    {
        $this->vars = $vars;
        $this->renderer = new Renderer();
        $this->response = new Response();
        $this->serviceContainer = new ApiServiceContainer();
        $this->charactersApiClient = $this->serviceContainer->getCharacterApiClient();
    }

    public function characters(string $query = ''): ResponseInterface
    {
        if (isset($this->vars['id'])) {
            $this->getSingleCharacter();
        } else {
            $methodName = __METHOD__;
            $pageName = substr(strrchr($methodName, '::'), 1);
            $uri = "character/?page={$this->vars['page']}&" . $query;
            $content = $this->api($uri);
            $html = $this->renderer->renderPage('Characters/Characters.twig', $content, $pageName, $this->vars['page']);
            $this->response->getBody()->write($html);
        }
        return $this->response->withHeader('Content-Type', 'text/html');
    }

    public function filter()
    {
        $queryParameters = [];
        foreach (['name', 'species', 'type', 'status', 'gender'] as $param) {
            if (!empty($_POST[$param])) {
                $queryParameters[$param] = $_POST[$param];
            }
        }
        $query = http_build_query($queryParameters);
        return $this->characters($query);
    }

    private function api($uri): array
    {
        return $this->charactersApiClient->getCharacters($uri);
    }

    private function getSingleCharacter(): void
    {
        $id = $this->vars['id'];
        $uri = "character/{$id}";
        $content = $this->charactersApiClient->getSingleCharacter($uri);
        $html = $this->renderer->renderSinglePage('Characters/SingleCharacter.twig', $content);
        $this->response->getBody()->write($html);
    }
}

