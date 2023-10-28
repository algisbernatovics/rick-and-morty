<?php

namespace App\Controllers;

use JetBrains\PhpStorm\NoReturn;

class HomeController
{
    #[NoReturn] public function Home(): void
    {
        $url = '/characters?page=1';
        header("Location: $url");
        exit;
    }

}