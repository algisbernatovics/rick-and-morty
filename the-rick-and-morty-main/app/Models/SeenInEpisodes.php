<?php

namespace App\Models;
class SeenInEpisodes
{
    protected string $episodeName;
    protected int $id;

    public function __construct(string $episodeName, int $id)
    {
        $this->episodeName = $episodeName;
        $this->id = $id;
    }

    public function getEpisodeName(): string
    {
        return $this->episodeName;
    }

    public function getId(): int
    {
        return $this->id;
    }

}