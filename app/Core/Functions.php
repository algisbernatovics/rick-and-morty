<?php

namespace App\Core;

class Functions
{
    public static function cutUri(string $episodeUri)
    {
        return substr($episodeUri, 32);
    }
}