<?php

namespace App\Models;

//CharactersRequest in locations and episodes.

class CharactersInLocation
{
    protected int $id;
    protected string $name;
    protected string $imageUrl;


    public function __construct( int $id,string $name, string $imageUrl,)
    {
        $this->name = $name;
        $this->imageUrl = $imageUrl;
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }
}