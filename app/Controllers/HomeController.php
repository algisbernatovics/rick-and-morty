<?php

namespace App\Controllers;

class HomeController
{
    public function Home()
    {
        $url = '/characters/1';
        header("Location: $url");
        exit;
    }

}