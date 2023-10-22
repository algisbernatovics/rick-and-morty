<?php

namespace App\API;

use App\Core\Functions;
use App\Models\Episodes;
use App\Models\CharactersInEpisode;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class EpisodesApiClient
{
    private const BASE_URI = 'https://rickandmortyapi.com/';
    private const API_PATH = 'api/';
    private object $client;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => self::BASE_URI]);
    }

    public function getEpisodes($uri): array
    {
        $cacheKey = 'episodes_' . md5($uri);

        return $this->tagCache->get($cacheKey, function ($item) use ($uri) {
            $response = $this->client->get(self::API_PATH . $uri);
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

    public function getSingleEpisode($uri): array
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

    private function request($episodeUri)
    {
        $response = $this->client->get(self::API_PATH . $episodeUri);
        return $response;
    }

    private function createEpisodeFromData($episodeData): Episodes
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

    private function createCharactersInFromData($character): CharactersInEpisode
    {
        return new CharactersInEpisode(
            $character->id,
            $character->name,
            $character->image
        );
    }
}
