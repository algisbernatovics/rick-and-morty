<?php

namespace App;

use App\Core\Cache;
use App\Core\Functions;
use App\Models\Characters;
use App\Models\Episodes;
use App\Models\Locations;
use GuzzleHttp\Client;

class ClientRequest
{
    private const BASE_URI = 'https://rickandmortyapi.com/api/';
    private object $client;
    private string $apiPageResponse;
    private string $apiDetailResponse;
    private string $uri;
    private string $pageCacheFileName;

    public function __construct($uri)
    {
        $this->uri = $uri;
        $this->pageCacheFileName = Functions::replaceSlash($this->uri);
        $this->client = new Client(['base_uri' => self::BASE_URI]);
    }

    public function getEpisodes(): array
    {
        $this->requestPages();
        $responseDecoded = json_decode($this->apiPageResponse)->results;
        $content = [];
        foreach ($responseDecoded as $response) {

            $content [] = new Episodes(
                $response->id,
                $response->name,
                $response->air_date,
                $response->episode,
                $response->characters,
                $response->url,
                $response->created,
            );
        }
        return $content;
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

    public function getCountOfPages()
    {
        $this->requestPages();
        return (json_decode($this->apiPageResponse)->info->pages);

    }

    public function getLocations(): array
    {
        $this->requestPages();
        $responseDecoded = json_decode($this->apiPageResponse)->results;
        $content = [];
        foreach ($responseDecoded as $response) {
            $content[] = new Locations(
                $response->id,
                $response->name,
                $response->dimension,
                $response->type,
                $response->residents,
                $response->url,
                $response->created,
            );
        }
        return $content;
    }

    public function getCharacters(): array
    {
        $this->requestPages();
        return $this->saveCharacters(json_decode($this->apiPageResponse)->results);
    }

    public function saveCharacters($responseDecoded): array
    {
        foreach ($responseDecoded as $response) {
            $episodeName = $this->getFirstEpisodeName($response->episode[0]);
            $content [] = new Characters(
                $response->id,
                $response->name,
                $response->status,
                $response->species,
                $response->type,
                $response->gender,
                $response->origin,
                $response->location,
                $response->image,
                $response->episode,
                $episodeName,
                $response->url,
                $response->created,
            );
        }
        return $content;
    }

    public function getFirstEpisodeName($episodeUri): string
    {
        $episodeUri = Functions::cutEpisodeUri($episodeUri);
        $this->requestDetails($episodeUri);
        return json_decode($this->apiDetailResponse)->name;
    }

    public function requestDetails($uri)
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
}