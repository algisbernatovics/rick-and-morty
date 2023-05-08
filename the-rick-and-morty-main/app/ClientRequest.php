<?php

namespace App;

use App\Core\Cache;
use App\Core\Functions;
use App\Models\Characters;
use App\Models\Episodes;
use App\Models\Locations;
use App\Models\SeenInEpisodes;
use GuzzleHttp\Client;

class ClientRequest
{
    private const BASE_URI = 'https://rickandmortyapi.com/api/';
    private object $client;
    private string $apiPageResponse;
    private string $apiDetailResponse;
    private string $uri;
    private string $pageCacheFileName;

    public function __construct(string $uri)
    {
        $this->uri = $uri;
        $this->pageCacheFileName = Functions::replaceSlash($this->uri);
        $this->client = new Client(['base_uri' => self::BASE_URI]);
    }

    public function getCountOfPages()
    {
        $this->requestPages();
        return (json_decode($this->apiPageResponse)->info->pages);
    }

    public function requestPages()
    {
        if (!Cache::has($this->pageCacheFileName)) {
            $this->apiPageResponse = $this->client->request('GET', $this->uri)->getBody()->getContents();
            echo 'Direct ';
        } else {
            $this->apiPageResponse = Cache::get($this->pageCacheFileName);
            echo 'Cache ';
        }
        Cache::remember($this->pageCacheFileName, $this->apiPageResponse);
    }

    public function getLocations(): array
    {
        $this->requestPages();
        return $this->saveLocations(json_decode($this->apiPageResponse)->results);
    }

    public function saveLocations(array $response): array
    {
        $content = [];
        foreach ($response as $location) {
            $content[] = new Locations(
                $location->id,
                $location->name,
                $location->dimension,
                $location->type,
                $location->residents,
                $location->url,
                $location->created,
            );
        }
        return $content;
    }

    public function getEpisodes(): array
    {
        $this->requestPages();
        return $this->saveEpisodes(json_decode($this->apiPageResponse)->results);
    }

    public function saveEpisodes(array $response): array
    {
        $content = [];
        foreach ($response as $episode) {
            $content [] = new Episodes(
                $episode->id,
                $episode->name,
                $episode->air_date,
                $episode->episode,
                $episode->characters,
                $episode->url,
                $episode->created,
            );
        }
        return $content;
    }

    public function getCharacters(): array
    {
        $this->requestPages();
        return $this->saveCharacters(json_decode($this->apiPageResponse)->results);
    }

    public function saveCharacters($response): array
    {
        foreach ($response as $character) {
            $episodeName = $this->getFirstSeenIn($character->episode[0]);
            $content [] = new Characters(
                $character->id,
                $character->name,
                $character->status,
                $character->species,
                $character->type,
                $character->gender,
                $character->origin,
                $character->location,
                $character->image,
                $character->episode,
                $episodeName,
                $character->url,
                $character->created,
            );
        }
        return $content;
    }

    public function getFirstSeenIn(string $episodeUri): string
    {
        $episodeUri = Functions::cutEpisodeUri($episodeUri);
        $this->requestDetails($episodeUri);
        return json_decode($this->apiDetailResponse)->name;
    }

    public function requestDetails(string $uri): void
    {
        $detailCacheFileName = Functions::replaceSlash($uri);
        if (!Cache::has($detailCacheFileName)) {
            $this->apiDetailResponse = $this->client->request('GET', $uri)->getBody()->getContents();
            echo 'Direct ';
        } else {
            $this->apiDetailResponse = Cache::get($detailCacheFileName);
            echo 'Cache ';
        }
        Cache::remember($detailCacheFileName, $this->apiDetailResponse);
    }

    public function getCharacter(): array
    {
        $this->requestPages();
        $response = (object)array('results' => json_decode($this->apiPageResponse));

        $seenInEpisodes = $this->saveInEpisodes(json_decode($this->apiPageResponse)->episode);

        return [$this->saveCharacters($response), $seenInEpisodes];
    }

    public function saveInEpisodes(array $episodesUri): array
    {
        $episodes = [];
        foreach ($episodesUri as $episodeUri) {
            $episodeUri = Functions::cutEpisodeUri($episodeUri);
            $this->requestDetails($episodeUri);
            $episodes[] = new SeenInEpisodes(json_decode($this->apiDetailResponse)->name);
        }
        return $episodes;
    }
}