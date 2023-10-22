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

    public function getCharacters($uri): array
    {
        $response = $this->client->get(self::API_PATH . $uri);
        $data = $this->decodeJsonResponse($response);

        $characters = [];

        foreach ($data->results as $characterData) {
            $episodeName = $this->getEpisodeName($characterData->episode[0]);
            $character = $this->createCharacterFromData($characterData, $episodeName);
            $characters[] = $character;
        }

        return ['cards' => $characters, 'info' => $data->info];
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
        $characterResponse = $this->request($uri);
        $characterData = $this->decodeJsonResponse($characterResponse);

        $episodeUris = $characterData->episode;
        $seenInEpisodes = $this->getSeenInEpisodes($episodeUris);
        $character = $this->createCharacterFromData($characterData, '');

        return ['card' => $character, 'info' => $seenInEpisodes];
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

    private function request($uri)
    {
        $response = $this->client->get(self::API_PATH . $uri);
        return $response;
    }
}
