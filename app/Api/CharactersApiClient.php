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

    public function __construct(TagAwareAdapter $tagCache, Client $client)
    {
        $this->client = $client;
        $this->tagCache = $tagCache;
    }

    public function getCharacters(string $uri): array
    {
        $cacheKey = 'characters_' . md5($uri);

        return $this->tagCache->get($cacheKey, function ($item) use ($uri) {

            $response = $this->request($uri);
            $data = $this->decodeJsonResponse($response);

            // 1. Collect all first-episode IDs
            $episodeIds = [];
            foreach ($data->results as $characterData) {
                if (!empty($characterData->episode)) {
                    $episodeIds[] = Functions::getEpisodeId($characterData->episode[0]);
                }
            }
            $episodeIds = array_unique($episodeIds);

            // 2. Bulk fetch episodes
            $episodesMap = [];
            if (!empty($episodeIds)) {
                $episodesUri = 'https://rickandmortyapi.com/api/episode/' . implode(',', $episodeIds);
                try {
                    $epResp = $this->request($episodesUri);
                    $epData = $this->decodeJsonResponse($epResp);
                    $epArray = is_array($epData) ? $epData : [$epData];

                    foreach ($epArray as $ep) {
                        $episodesMap[$ep->id] = $ep->name;
                    }
                }
                catch (Exception $e) {
                // ignore or log
                }
            }

            $characters = [];

            foreach ($data->results as $characterData) {
                $firstEpId = !empty($characterData->episode) ?Functions::getEpisodeId($characterData->episode[0]) : null;
                $episodeName = $episodesMap[$firstEpId] ?? 'Unknown';

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

    public function getSingleCharacter(string $uri): array
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

    private function createCharacterFromData(\stdClass $characterData, string $episodeName): Characters
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
        $episodeIds = [];
        foreach ($episodeUris as $episodeUri) {
            $episodeIds[] = Functions::getEpisodeId($episodeUri);
        }

        if (empty($episodeIds)) {
            return [];
        }

        $episodes = [];
        // Bulk fetch: https://rickandmortyapi.com/api/episode/[1,2,3]
        $uri = 'https://rickandmortyapi.com/api/episode/' . implode(',', $episodeIds);

        try {
            $response = $this->request($uri);
            $data = $this->decodeJsonResponse($response);

            // API returns a single object if only 1 ID is requested, otherwise an array
            $episodesData = is_array($data) ? $data : [$data];

            foreach ($episodesData as $episodeData) {
                $episodes[] = new SeenInEpisodes(
                    $episodeData->name,
                    $episodeData->id
                    );
            }
        }
        catch (Exception $e) {
        // Fallback or error handling
        }

        return $episodes;
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
}