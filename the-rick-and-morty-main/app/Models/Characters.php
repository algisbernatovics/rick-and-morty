<?php

namespace App\Models;

class Characters
{
    protected string $id;
    protected string $name;
    protected string $status;
    protected string $species;
    protected string $type;
    protected string $gender;
    protected object $origin;

    protected object $location;
    protected string $image;
    protected array $episode;
    protected string $episodeName;
    protected string $created;
    protected string $url;

    public function __construct(
        int    $id, string $name, string $status, string $species, string $type, string $gender, object $origin,
        object $location, string $image, array $episode, string $episodeName, string $url, string $created)
    {
        $this->id = $id;
        $this->name = $name;
        $this->status = $status;
        $this->species = $species;
        $this->type = $type;
        $this->gender = $gender;
        $this->origin = $origin;
        $this->location = $location;
        $this->image = $image;
        $this->episode = $episode;
        $this->episodeName = $episodeName;
        $this->url = $url;
        $this->created = $created;
    }

    public function getLocation(): object
    {
        return $this->location;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function getEpisode(): array
    {
        return $this->episode;
    }

    public function getCreated(): string
    {
        return $this->created;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getSpecies(): string
    {
        return $this->species;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getEpisodeName(): string
    {
        return $this->episodeName;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function getOrigin(): object
    {
        return $this->origin;
    }

}