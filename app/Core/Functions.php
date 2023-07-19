<?php

namespace App\Core;

class Functions
{
    public static function replaceSlash(string $uri)
    {
        return str_replace('/', '', $uri);
    }

    public static function cutEpisodeUri(string $episodeUri)
    {
        return substr($episodeUri, 32);
    }

    public static function cleanDateStr(string $expires_at)
    {
        return preg_replace('/[^0-9]/', " ", $expires_at, -1);
    }

}