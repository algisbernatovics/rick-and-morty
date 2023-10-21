<?php

namespace App\Controllers;

class HomeController
{
    public function Home()
    {
        $url = '/character?page=1';
        header("Location: $url");
        exit;
    }

}