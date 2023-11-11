<?php

namespace App\Controllers;

use App\Api\CharactersApiClient;
use App\Core\ApiServiceContainer;
use GuzzleHttp\Psr7\ServerRequest;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use App\Core\Renderer;

class CharactersController
{
    private const PATH = 'character/?';
    private const PAGENAME ='characters';
    private Renderer $renderer;
    private Response $response;
    private ServerRequest $request;
    private ApiServiceContainer $serviceContainer;
    private CharactersApiClient $charactersApiClient;
    private array $vars;

    public function __construct(ServerRequest $request, $vars)
    {
        $this->vars = $vars;
        $this->request = $request;
        $this->renderer = new Renderer();
        $this->response = new Response();
        $this->serviceContainer = new ApiServiceContainer();
        $this->charactersApiClient = $this->serviceContainer->getCharacterApiClient();
    }

    public function characters(array $filterQuery = []): ResponseInterface
    {
        if (isset($this->vars['id'])) {
            $this->getSingleCharacter();
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

            $content = $this->charactersApiClient->getCharacters($uri);
            $html = $this->renderer->renderPage('Characters/Characters.twig', $content, self::PAGENAME, $page, $queryShadow);
            $this->response->getBody()->write($html);
        }
        return $this->response->withHeader('Content-Type', 'text/html');
    }

    public function filter(): ResponseInterface
    {
        $postData = $this->request->getParsedBody();

        $queryParameters = [];
        foreach (['name', 'species', 'type', 'status', 'gender'] as $param) {
            if (!empty($postData[$param])) {
                $queryParameters[$param] = $postData[$param];
            }
        }

        return $this->characters($queryParameters);
    }


    private function getSingleCharacter(): void
    {
        $id = $this->vars['id'];
        $uri = "character/{$id}";
        $content = $this->charactersApiClient->getSingleCharacter($uri);
        $html = $this->renderer->renderSinglePage('Characters/SingleCharacter.twig', $content,self::PAGENAME);
        $this->response->getBody()->write($html);
    }
}

