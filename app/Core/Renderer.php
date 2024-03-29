<?php

namespace App\Core;

use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

class Renderer
{
    protected string $template;
    protected Environment $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader('../app/Views');
        $this->twig = new Environment($loader, ['debug' => true]);
        $this->twig->addExtension(new DebugExtension());
    }

    public function renderPage(string $template, array $content, string $pageName, int $page,string $queryShadow): string
    {
        return $this->twig->render(
            $template,
            ['cards' => $content['cards'], 'info' => $content['info'], 'pageName' => $pageName, 'page' => $page, 'query' => $queryShadow]);
    }

    public function renderSinglePage(string $template, array $content,string $pageName): string
    {
        return $this->twig->render(
            $template,
            ['card' => $content['card'], 'info' => $content['info'],'pageName'=>$pageName]);
    }

    public function error(string $template, int $errorCode): string
    {
        return $this->twig->render(
            $template,
            ['errorCode' => $errorCode]);
    }

    public function exception(string $template, $exception): void
    {
        $this->twig->load($template)->display(['exception' => $exception]);
    }
}