<?php

namespace App\API;

use App\Core\Functions;
use App\Models\CharactersInLocation;
use App\Models\Locations;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;

class LocationsApiClient
{
    private const BASE_URI = 'https://rickandmortyapi.com/';
    private const API_PATH = 'api/';
    private Client $client;

    public function __construct(TagAwareAdapter $tagCache)
    {
        $this->client = new Client(['base_uri' => self::BASE_URI]);
        $this->tagCache = $tagCache;
    }

    public function getLocations(string $uri): array
    {
        $cacheKey = 'locations_' . md5($uri);

        return $this->tagCache->get($cacheKey, function ($item) use ($uri) {
            $response = $this->client->get(self::API_PATH . $uri);
            $data = $this->decodeJsonResponse($response);

            $locations = [];

            foreach ($data->results as $locationData) {
                $location = $this->createLocationFromData($locationData);
                $locations[] = $location;
            }

            return ['cards' => $locations, 'info' => $data->info];
        });
    }

    private function decodeJsonResponse(ResponseInterface $response): object
    {
        $jsonContent = $response->getBody()->getContents();
        return json_decode($jsonContent);
    }

    public function getSingleLocation(string $uri): array
    {
        $cacheKey = 'single_location_' . md5($uri);

        return $this->tagCache->get($cacheKey, function ($item) use ($uri) {
            $locationResponse = $this->request($uri);
            $locationData = $this->decodeJsonResponse($locationResponse);
            $residents = $locationData->residents;
            $residents = $this->getSeenInEpisode($residents);
            $location = $this->createLocationFromData($locationData, '');
            return ['card' => $location, 'info' => $residents];
        });
    }

    private function createLocationFromData(object $locationData): Locations
    {
        return new Locations(
            $locationData->id,
            $locationData->name,
            $locationData->dimension,
            $locationData->type,
            $locationData->residents,
            $locationData->url,
            $locationData->created
        );
    }

    private function getSeenInEpisode(array $episodeUris): array
    {
        $residents = [];
        foreach ($episodeUris as $episodeUri) {
            $episodeUri = Functions::cutUri($episodeUri);
            $episodeResponse = $this->request($episodeUri);
            $episodeData = $this->decodeJsonResponse($episodeResponse);
            $residents[] = new CharactersInLocation(
                $episodeData->id,
                $episodeData->name,
                $episodeData->image
            );
        }
        return $residents;
    }

    private function request(string $uri): ResponseInterface
    {
        return $this->client->get(self::API_PATH . $uri);
    }
}
