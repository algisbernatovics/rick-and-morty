<?php

namespace App\Core;

class Functions
{
    public static function cutUri(string $episodeUri)
    {
        return substr($episodeUri, 32);
    }

    public static function getEpisodeId(string $uri): string
    {
        $parts = explode('/', rtrim($uri, '/'));
        return end($parts);
    }
}