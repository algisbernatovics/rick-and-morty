<?php

namespace App\Core;


use Carbon;

class Cache
{
    public static function remember(string $key, string $data, int $ttl = 120): void
    {
        $expire_at = Carbon\CarbonImmutable::now()->addSeconds($ttl);
        $cacheFile = '../tmp/' . $key;
        file_put_contents($cacheFile, json_encode([
            'expires_at' => $expire_at,
            'content' => $data
        ]));
    }

    public static function get(string $key): ?string
    {
        if (!file_exists('../tmp/' . $key)) {
            return null;
        }
        $content = json_decode(file_get_contents('../tmp/' . $key));
        return $content->content;
    }

    public static function has(string $key): bool
    {
        if (!file_exists('../tmp/' . $key)) {
            return false;
        }
        $content = json_decode(file_get_contents('../tmp/' . $key));

        $str = preg_replace('/[^0-9]/', " ", $content->expires_at, -1);
        $exp = explode(' ', $str);
        $expires_at = Carbon\CarbonImmutable::create($exp[0], $exp[1], $exp[2], $exp[3], $exp[4], $exp[5]);
        
        return ($expires_at > Carbon\CarbonImmutable::now()->subHour(3));
    }
}