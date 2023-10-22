<?php

namespace App\API;

use App\Core\Functions;
use App\Models\Characters;
use App\Models\SeenInEpisodes;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class CharactersRequest
{
    private const BASE_URI = 'https://rickandmortyapi.com/';
    private const API_PATH = 'api/';
    private object $client;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => self::BASE_URI]);
    }

    public function getCharacters($uri)
    {

        $response = $this->client->get(self::API_PATH . $uri);
        $response = $this->decodeJsonResponse($response);

        $info = $response->info;

        $characters = [];

        foreach ($response->results as $characterData) {
            $episodeName = $this->getEpisodeName($characterData->episode[0]);

            $characters[] = new Characters(
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
        return ['cards' => $characters, 'info' => $info];
    }

    private function decodeJsonResponse(ResponseInterface $response)
    {
        $jsonContent = $response->getBody()->getContents();
        return json_decode($jsonContent, false);
    }

    private function getEpisodeName(string $episodeUri): string
    {
        $episodeUri = Functions::cutEpisodeUri($episodeUri);
        $episodeResponse = $this->client->request('GET', self::API_PATH . $episodeUri);
        $episodeData = $this->decodeJsonResponse($episodeResponse);

        return $episodeData->name;
    }

    public function getSingleCharacter($uri): array
    {
        $characterResponse = $this->requestCharacterData($uri);
        $characterData = $this->decodeJsonResponse($characterResponse);

        $episodeUris = $characterData->episode;

        $seenInEpisodes = $this->getSeenInEpisodes($episodeUris);

        $character = new Characters($characterData->id,
            $characterData->name,
            $characterData->status,
            $characterData->species,
            $characterData->type,
            $characterData->gender,
            $characterData->origin,
            $characterData->location,
            $characterData->image,
            $characterData->episode,
            '',
            $characterData->url,
            $characterData->created
        );

        return ['card' => $character, 'info' => $seenInEpisodes];
    }

    private function requestCharacterData($characterUri)
    {
        $response = $this->client->get(self::API_PATH . $characterUri);
        return $response;
    }

    private function getSeenInEpisodes(array $episodeUris)
    {
        $episodes = [];
        foreach ($episodeUris as $episodeUri) {
            $episodeUri = Functions::cutUri($episodeUri);
            $episodeResponse = $this->requestEpisodeData($episodeUri);
            $episodeData = $this->decodeJsonResponse($episodeResponse);
            $episodes[] = new SeenInEpisodes(
                $episodeData->name,
                $episodeData->id
            );
        }
        return $episodes;
    }

    private function requestEpisodeData($episodeUri)
    {
        $response = $this->client->get(self::API_PATH . $episodeUri);
        return $response;
    }

}
