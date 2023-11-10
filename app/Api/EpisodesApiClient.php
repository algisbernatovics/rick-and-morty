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

    public function __construct(TagAwareAdapter $tagCache,Client $client)
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
        } catch (Exception $exception) {
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
        $charactersInEpisode = [];

        foreach ($characterUris as $characterUri) {
            $characterUri = Functions::cutUri($characterUri);
            $characterData = $this->request($characterUri);
            $character = $this->decodeJsonResponse($characterData);
            $charactersInEpisode[] = $this->createCharactersInFromData($character);
        }

        return $charactersInEpisode;
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
