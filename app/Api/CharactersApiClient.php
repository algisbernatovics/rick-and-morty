<?php

namespace App\Api;

use App\Controllers\ErrorsController;
use App\Core\Functions;
use App\Models\Characters;
use App\Models\SeenInEpisodes;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;

class CharactersApiClient
{
    private TagAwareAdapter $tagCache;
    private Client $client;
    public function __construct(TagAwareAdapter $tagCache,Client $client)
    {
        $this->client = $client;
        $this->tagCache = $tagCache;
    }

    public function getCharacters($uri): array
    {
        $cacheKey = 'characters_' . md5($uri);

        return $this->tagCache->get($cacheKey, function ($item) use ($uri) {

            $response = $this->request($uri);
            $data = $this->decodeJsonResponse($response);

            $characters = [];

            foreach ($data->results as $characterData) {
                $episodeName = $this->getEpisodeName($characterData->episode[0]);
                $character = $this->createCharacterFromData($characterData, $episodeName);
                $characters[] = $character;
            }

            return ['cards' => $characters, 'info' => $data->info];
        });
    }

    private function decodeJsonResponse(ResponseInterface $response)
    {
        $jsonContent = $response->getBody()->getContents();
        return json_decode($jsonContent);
    }

    private function getEpisodeName(string $episodeUri): string
    {
        $episodeUri = Functions::cutUri($episodeUri);
        $episodeResponse = $this->request($episodeUri);
        $episodeData = $this->decodeJsonResponse($episodeResponse);

        return $episodeData->name;
    }

    public function getSingleCharacter($uri): array
    {
        $cacheKey = 'single_character_' . md5($uri);

        return $this->tagCache->get($cacheKey, function ($item) use ($uri) {
            $characterResponse = $this->request($uri);
            $characterData = $this->decodeJsonResponse($characterResponse);

            $episodeUris = $characterData->episode;
            $seenInEpisodes = $this->getSeenInEpisodes($episodeUris);
            $character = $this->createCharacterFromData($characterData, '');

            return ['card' => $character, 'info' => $seenInEpisodes];
        });
    }

    private function createCharacterFromData($characterData, $episodeName): Characters
    {
        return new Characters(
            $characterData->id,
            $characterData->name,
            $characterData->status,
            $characterData->species,
            $characterData->type,
            $characterData->gender,
            $characterData->origin,
            $characterData->location,
            $characterData->image,
            $characterData->episode,
            $episodeName,
            $characterData->url,
            $characterData->created
        );
    }

    private function getSeenInEpisodes(array $episodeUris): array
    {
        $episodes = [];
        foreach ($episodeUris as $episodeUri) {
            $episodeUri = Functions::cutUri($episodeUri);
            $episodeResponse = $this->request($episodeUri);
            $episodeData = $this->decodeJsonResponse($episodeResponse);
            $episodes[] = new SeenInEpisodes(
                $episodeData->name,
                $episodeData->id
            );
        }
        return $episodes;
    }

    private function request($uri): ResponseInterface
    {
        try {
            $response = $this->client->get($uri);
            return $response;
        } catch (Exception $exception) {
            $errorsController = new ErrorsController(500);
            $errorsController->exception($exception);
            exit;
        }
    }

}
