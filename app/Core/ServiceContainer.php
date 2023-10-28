<?php

namespace App\Core;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use App\API\CharactersApiClient;
use App\API\EpisodesApiClient;
use App\API\LocationsApiClient;

class ServiceContainer
{
    private FilesystemAdapter $cache;
    private TagAwareAdapter $tagCache;

    public function __construct()
    {
        $this->cache = new FilesystemAdapter($_ENV['CACHE_DIR'], $_ENV['CACHE_TTL'], __DIR__ . $_ENV['CACHE_PATH']);
        $this->tagCache = new TagAwareAdapter($this->cache);
    }

    public function getCharacterApiClient(): CharactersApiClient
    {
        return new CharactersApiClient($this->tagCache);
    }

    public function getLocationApiClient(): LocationsApiClient
    {
        return new LocationsApiClient($this->tagCache);
    }
    public function getEpisodesRequest(): EpisodesApiClient
    {
        return new EpisodesApiClient($this->tagCache);
    }
}
