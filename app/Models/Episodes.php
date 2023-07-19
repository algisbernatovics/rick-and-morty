<?php

namespace App\Models;

class Episodes
{
    protected string $id;
    protected string $name;
    protected string $airDate;
    protected string $episode;
    protected array $characters;
    protected string $created;
    protected string $url;

    public function __construct
    (
        int $id, string $name, string $airDate, string $episode, array $characters, string $url, string $created
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->airDate = $airDate;
        $this->episode = $episode;
        $this->characters = $characters;
        $this->url = $url;
        $this->created = $created;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAirDate(): string
    {
        return $this->airDate;
    }

    public function getEpisode(): string
    {
        return $this->episode;
    }

    public function getCharacters(): array
    {
        return $this->characters;
    }

    public function getCreated(): string
    {
        return $this->created;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

}