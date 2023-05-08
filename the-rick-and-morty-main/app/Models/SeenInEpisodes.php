<?php

namespace App\Models;
class SeenInEpisodes
{
    protected string $episodeName;

    public function __construct(string $episodeName)
    {
        $this->episodeName = $episodeName;
    }

    public function getEpisodeName(): string
    {
        return $this->episodeName;
    }

}