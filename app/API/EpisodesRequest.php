<?php

namespace App\API;

use App\Core\Functions;
use App\Models\Characters;
use App\Models\CharactersIn;
use App\Models\Episodes;
use App\Models\SeenInEpisodes;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class EpisodesRequest
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
        $response = $this->client->get(self::API_PATH . $uri);
        $response = $this->decodeJsonResponse($response);

        $info = $response->info;

        $episodes = [];

        foreach ($response->results as $episodeData) {
            $episodes[] = new Episodes(
                $episodeData->id,
                $episodeData->name,
                $episodeData->air_date,
                $episodeData->episode,
                $episodeData->characters,
                $episodeData->url,
                $episodeData->created,
            );

        }
        return ['cards' => $episodes, 'info' => $info];
    }

    private function decodeJsonResponse(ResponseInterface $response)
    {
        $jsonContent = $response->getBody()->getContents();
        return json_decode($jsonContent, false);
    }

    public function getSingleEpisode($uri): array
    {
        $episodeResponse = $this->requestDetails($uri);
        $episodeData = $this->decodeJsonResponse($episodeResponse);
        $charactersInEpisode = $this->getSeenInEpisodes($episodeData->characters);

        $episode = new Episodes(
            $episodeData->id,
            $episodeData->name,
            $episodeData->air_date,
            $episodeData->episode,
            $episodeData->characters,
            $episodeData->url,
            $episodeData->created
        );

        return ['card' => $episode, 'info' => $charactersInEpisode];
    }

    private function requestDetails($episodeUri)
    {
        $response = $this->client->get(self::API_PATH . $episodeUri);
        return $response;
    }
    private function getSeenInEpisodes(array $characterUris): array
    {
        $charactersInEpisode = [];

        foreach ($characterUris as $characterUri) {

            $characterUri = Functions::cutUri($characterUri);
            $characterData = $this->requestDetails($characterUri);
            $character = $this->decodeJsonResponse($characterData);
            $charactersInEpisode[] = new CharactersIn(
                $character->id,
                $character->name,
                $character->image

            );
        }
        return $charactersInEpisode;
    }

}
