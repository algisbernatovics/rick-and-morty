<?php

namespace App\Models;
class Pages
{
    protected int $pages;

    public function __construct($pages)
    {
        $this->pages = $pages;
    }

    public function getPages()
    {
        return $this->pages;
    }
}