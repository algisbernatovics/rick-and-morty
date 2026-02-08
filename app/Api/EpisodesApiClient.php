<?php

namespace App\Api;

use App\Controllers\ErrorsController;
use App\Core\Functions;
use App\Models\Episodes;
use App\Models\CharactersInEpisode;
use Exception;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;

class EpisodesApiClient
{
    private TagAwareAdapter $tagCache;
    private Client $client;

    public function __construct(TagAwareAdapter $tagCache, Client $client)
    {
        $this->client = $client;
        $this->tagCache = $tagCache;
    }

    public function getEpisodes(string $uri): array
    {
        $cacheKey = 'episodes_' . md5($uri);

        return $this->tagCache->get($cacheKey, function ($item) use ($uri) {
            $response = $this->request($uri);
            $data = $this->decodeJsonResponse($response);

            $info = $data->info;

            $episodes = [];

            foreach ($data->results as $episodeData) {
                $episodes[] = $this->createEpisodeFromData($episodeData);
            }

            return ['cards' => $episodes, 'info' => $info];
        });
    }

    private function decodeJsonResponse(ResponseInterface $response)
    {
        $jsonContent = $response->getBody()->getContents();
        return json_decode($jsonContent);
    }

    public function getSingleEpisode(string $uri): array
    {
        $cacheKey = 'single_episode_' . md5($uri);

        return $this->tagCache->get($cacheKey, function ($item) use ($uri) {
            $episodeResponse = $this->request($uri);
            $episodeData = $this->decodeJsonResponse($episodeResponse);
            $charactersInEpisode = $this->getCharactersInEpisode($episodeData->characters);

            $episode = $this->createEpisodeFromData($episodeData);

            return ['card' => $episode, 'info' => $charactersInEpisode];
        });
    }

    private function request(string $uri): ResponseInterface
    {
        try {
            $response = $this->client->get($uri);
            return $response;
        }
        catch (Exception $exception) {
            $errorsController = new ErrorsController();
            $errorsController->exception($exception);
            exit;
        }
    }

    private function createEpisodeFromData(\stdClass $episodeData): Episodes
    {
        return new Episodes(
            $episodeData->id,
            $episodeData->name,
            $episodeData->air_date,
            $episodeData->episode,
            $episodeData->characters,
            $episodeData->url,
            $episodeData->created
            );
    }

    private function getCharactersInEpisode(array $characterUris): array
    {
        $characterIds = [];
        foreach ($characterUris as $characterUri) {
            $characterIds[] = Functions::getEpisodeId($characterUri); // Assuming getEpisodeId extracts the ID
        }

        if (empty($characterIds)) {
            return [];
        }

        // Bulk fetch: https://rickandmortyapi.com/api/character/[1,2,3]
        $uri = 'https://rickandmortyapi.com/api/character/' . implode(',', $characterIds);

        try {
            $response = $this->request($uri);
            $data = $this->decodeJsonResponse($response);

            // API returns a single object if only 1 ID is requested, otherwise an array
            $charactersData = is_array($data) ? $data : [$data];

            $charactersInEpisode = [];
            foreach ($charactersData as $character) {
                $charactersInEpisode[] = $this->createCharactersInFromData($character);
            }

            return $charactersInEpisode;
        }
        catch (Exception $e) {
            // Fallback or error handling
            return [];
        }
    }

    private function createCharactersInFromData(\stdClass $character): CharactersInEpisode
    {
        return new CharactersInEpisode(
            $character->id,
            $character->name,
            $character->image
            );
    }
}