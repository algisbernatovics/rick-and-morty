<?php

namespace App\Controllers;

use App\Core\Renderer;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class ErrorsController
{
    private Renderer $renderer;
    private int $errorCode;

    public function __construct(int $errorCode=500)
    {
        $this->renderer = new Renderer();
        $this->errorCode = $errorCode;
    }

    public function error(): ResponseInterface
    {
        $html = $this->renderer->error('Errors/Error.twig', $this->errorCode);
        $response = new Response(200, [], $html);
        return $response->withHeader('Content-Type', 'text/html');
    }
    public function exception($exception)
    {
        $html = $this->renderer->exception('Errors/Error.twig', $exception);
        $response = new Response(200, [], $html);
        return $response->withHeader('Content-Type', 'text/html');
    }
}
