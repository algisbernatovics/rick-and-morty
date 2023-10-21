<?php

namespace App;

use App\Controllers\Controller;
use App\Core\Cache;
use App\Core\Functions;
use App\Models\Characters;
use App\Models\CharactersIn;
use App\Models\Episodes;
use App\Models\Locations;
use App\Models\SeenInEpisodes;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Nyholm\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;

class ClientRequest
{
    private const BASE_URI = 'https://rickandmortyapi.com/';
    private const API_PATH = 'api/';
    private object $client;
//    private string $apiPageResponse;
//    private string $apiDetailResponse;
    private string $target;

    public function __construct($target)
    {

        $this->target = $target;
        $this->client = new Client(['base_uri' => self::BASE_URI]);
    }

    public function getCharacters()
    {
        $response = $this->client->get(self::API_PATH . $this->target);
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
        return ['characters' => $characters, 'info' => $info];
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


//    public function getSearchResults(): array
//    {
//        $this->requestPages();
//        $pages = $this->getCountOfPages();
//        $searchResults = $this->saveCharacters(json_decode($this->apiPageResponse)->results);
//        return [$searchResults, $pages];
//    }
//
//    public function requestPages()
//    {
//        $pageCacheFileName = Functions::replaceSlash($this->uri);
//        if (!Cache::has($pageCacheFileName)) {
//            try {
//                $this->apiPageResponse = $this->client->request('GET', $this->uri)->getBody()->getContents();
//            } catch (GuzzleException $e) {
//                (new Controller())->error();
//            }
//        } else {
//            $this->apiPageResponse = Cache::get($pageCacheFileName);
//        }
//        Cache::remember($pageCacheFileName, $this->apiPageResponse);
//    }
//
//    public function getCountOfPages()
//    {
//        $this->requestPages();
//        return (json_decode($this->apiPageResponse)->info->pages);
//    }
//
//    public function saveCharacters($response): array
//    {
//        foreach ($response as $character) {
//            $episodeName = $this->getFirstSeenIn($character->episode[0]);
//            $content [] = new Characters(
//                $character->id,
//                $character->name,
//                $character->status,
//                $character->species,
//                $character->type,
//                $character->gender,
//                $character->origin,
//                $character->location,
//                $character->image,
//                $character->episode,
//                $episodeName,
//                $character->url,
//                $character->created,
//            );
//        }
//        return $content;
//    }
//
//    public function getFirstSeenIn(string $episodeUri): string
//    {
//        $episodeUri = Functions::cutEpisodeUri($episodeUri);
//        $this->requestDetails($episodeUri);
//        return (json_decode($this->apiDetailResponse)->name);
//    }
//
//    public function requestDetails(string $uri): void
//    {
//        $detailCacheFileName = Functions::replaceSlash($uri);
//        if (!Cache::has($detailCacheFileName)) {
//            try {
//                $this->apiDetailResponse = $this->client->request('GET', $uri)->getBody()->getContents();
//            } catch (GuzzleException $e) {
//                (new Controller())->error();
//            }
//        } else {
//            $this->apiDetailResponse = Cache::get($detailCacheFileName);
//        }
//        Cache::remember($detailCacheFileName, $this->apiDetailResponse);
//    }
//
//    public function getEpisodes(): array
//    {
//        $this->requestPages();
//        return $this->saveEpisodes(json_decode($this->apiPageResponse)->results);
//    }
//
//    public function saveEpisodes($response): array
//    {
//        $content = [];
//        foreach ($response as $episode) {
//            $content [] = new Episodes(
//                $episode->id,
//                $episode->name,
//                $episode->air_date,
//                $episode->episode,
//                $episode->characters,
//                $episode->url,
//                $episode->created,
//            );
//        }
//        return $content;
//    }
//
//    public function getSingleEpisode(): array
//    {
//        $this->requestPages();
//        $response = (object)array('results' => json_decode($this->apiPageResponse));
//        $charactersInEpisode = $this->saveCharactersInEpisode(json_decode($this->apiPageResponse)->characters);
//        return [$this->saveEpisodes($response), $charactersInEpisode];
//    }
//
//    public function saveCharactersInEpisode(array $charactersUri): array
//    {
//        $episodes = [];
//        foreach ($charactersUri as $characterUri) {
//            $characterUri = Functions::cutEpisodeUri($characterUri);
//            $this->requestDetails($characterUri);
//            $episodes[] = new CharactersIn(
//                json_decode($this->apiDetailResponse)->name,
//                json_decode($this->apiDetailResponse)->image,
//                json_decode($this->apiDetailResponse)->id
//            );
//        }
//        return $episodes;
//    }
//
//    public function getLocations(): array
//    {
//        $this->requestPages();
//        return $this->saveLocations(json_decode($this->apiPageResponse)->results);
//    }
//
//    public function saveLocations($response): array
//    {
//        $content = [];
//        foreach ($response as $location) {
//            $content[] = new Locations(
//                $location->id,
//                $location->name,
//                $location->dimension,
//                $location->type,
//                $location->residents,
//                $location->url,
//                $location->created,
//            );
//        }
//        return $content;
//    }
//
//    public function getSingleLocation(): array
//    {
//        $this->requestPages();
//        $response = (object)array('results' => json_decode($this->apiPageResponse));
//        $charactersInEpisode = $this->saveCharactersInEpisode(json_decode($this->apiPageResponse)->residents);
//        return [$this->saveLocations($response), $charactersInEpisode];
//    }
//
//    public function getCharacters(): array
//    {
//        $this->requestPages();
//        return $this->saveCharacters(json_decode($this->apiPageResponse)->results);
//    }
//
//    public function getSingleCharacter(): array
//    {
//        $this->requestPages();
//        $response = (object)array('results' => json_decode($this->apiPageResponse));
//        $seenInEpisodes = $this->saveSeenInEpisodes(json_decode($this->apiPageResponse)->episode);
//        return [$this->saveCharacters($response), $seenInEpisodes];
//    }
//
//    public function saveSeenInEpisodes(array $episodesUri): array
//    {
//        $episodes = [];
//        foreach ($episodesUri as $episodeUri) {
//            $episodeUri = Functions::cutEpisodeUri($episodeUri);
//            $this->requestDetails($episodeUri);
//            $episodes[] = new SeenInEpisodes(
//                json_decode($this->apiDetailResponse)->name,
//                json_decode($this->apiDetailResponse)->id
//            );
//        }
//        return $episodes;
//    }
}






