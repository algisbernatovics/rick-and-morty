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
    private string $apiResponse;
    private string $uri;
    private ?string $episodeResponse;

    public function __construct($uri)
    {
        $this->uri = $uri;
        $this->client = new Client(['base_uri' => self::BASE_URI]);
    }

    public function getEpisodes(): array
    {
        $this->cachePage(Functions::replaceSlash($this->uri));
        $responseDecoded = json_decode($this->apiResponse)->results;
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
                $this->getCountOfPages(),
            );
        }
        return $content;
    }

    public function cachePage($pageCacheFileName)
    {
        if (!Cache::has($pageCacheFileName)) {
            $this->apiResponse = $this->client->request('GET', $this->uri)->getBody()->getContents();
            echo 'New Direct Request Cache Page ';
        } else {
            $this->apiResponse = Cache::get($pageCacheFileName);
            echo 'Cache Request ';
        }
        Cache::remember($pageCacheFileName, $this->apiResponse);
    }

    public function getCountOfPages()
    {
        $count = $this->client->request('GET', $this->uri);
        return (json_decode($count->getBody()->getContents())->info->pages);
    }

    public function getLocations(): array
    {
        $this->cachePage(Functions::replaceSlash($this->uri));
        $responseDecoded = json_decode($this->apiResponse)->results;
        $content = [];
        foreach ($responseDecoded as $response) {
            $content [] = new Locations(
                $response->id,
                $response->name,
                $response->dimension,
                $response->type,
                $response->residents,
                $response->url,
                $response->created,
                $this->getCountOfPages(),
            );
        }
        return $content;
    }

    public function getCharacters(): array
    {
        $this->cachePage(Functions::replaceSlash($this->uri));
        return $this->saveCharacters(json_decode($this->apiResponse)->results);
    }

    public function saveCharacters($responseDecoded): array
    {
//        if (array_key_exists('results', $responseDecoded)) {
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
                $this->getCountOfPages()
            );
        }
        return $content;
    }

    public function getFirstEpisodeName($episodeUri): string
    {
        $episodeUri = Functions::cutEpisodeUri($episodeUri);
        $this->cacheEpisode($episodeUri, Functions::replaceSlash($episodeUri));
        return json_decode($this->episodeResponse)->name;
    }

    public function cacheEpisode($episodeUri, $episodeCacheFileName)
    {
        if (!Cache::has($episodeCacheFileName)) {
            $this->episodeResponse = $this->client->request('GET', $episodeUri)->getBody()->getContents();
        } else {
            $this->episodeResponse = Cache::get($episodeCacheFileName);
        }
        Cache::remember($episodeCacheFileName, $this->episodeResponse);
    }

    public function getCharacter(): array
    {
        $this->cachePage(Functions::replaceSlash($this->uri));

        return $this->saveCharacters(json_decode($this->apiResponse));
    }
}