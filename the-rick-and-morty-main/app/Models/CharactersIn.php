<?php

namespace App\Models;

class CharactersIn
{
    protected string $name;
    protected string $imageUrl;
    protected int $id;

    public function __construct(string $name, string $imageUrl,int $id)
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