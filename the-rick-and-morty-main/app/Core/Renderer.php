<?php

namespace App\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Renderer
{
    protected string $template;
    protected Environment $twig;

    protected array $content;

    protected int $pages;

    public function __construct($content, $pages)
    {
        $this->pages = $pages;
        $this->content = $content;
        $loader = new FilesystemLoader('../public/Views');
        $this->twig = new Environment($loader);
    }

    public function render($template): string
    {
        return $this->twig->render($template, ['cards' => $this->content, 'pages' => $this->pages]);
    }
}