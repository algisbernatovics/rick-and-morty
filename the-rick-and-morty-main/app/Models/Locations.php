<?php

namespace App\Models;

class Locations
{

    protected string $id;
    protected string $name;
    protected string $dimension;
    protected string $type;
    protected array $residents;
    protected string $created;
    protected string $url;

    public function __construct($id, $name, $dimension, $type, $residents, $url, $created)
    {
        $this->id = $id;
        $this->name = $name;
        $this->dimension = $dimension;
        $this->type = $type;
        $this->residents = $residents;
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

    public function getDimension(): string
    {
        return $this->dimension;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getResidents(): array
    {
        return $this->residents;
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