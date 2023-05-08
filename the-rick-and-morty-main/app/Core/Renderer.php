<?php

namespace App\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Renderer
{
    protected string $template;
    protected Environment $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader('../public/Views');
        $this->twig = new Environment($loader);
    }

    public function renderPage(string $template, array $content, int $pages): string
    {
        return $this->twig->render(
            $template,
            ['cards' => $content, 'pages' => $pages]);
    }

    public function renderSinglePage(string $template, array $content, array $seenInLocations): string
    {
        return $this->twig->render(
            $template,
            ['cards' => $content, 'seenIn' => $seenInLocations]);
    }
}