<?php

namespace App\Models;
class Pages
{
    protected int $pages;

    public function __construct(int $pages)
    {
        $this->pages = $pages;
    }

    public function getPages(): int
    {
        return $this->pages;
    }
}