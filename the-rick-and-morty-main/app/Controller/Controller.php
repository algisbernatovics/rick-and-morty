<?php

namespace App\Controller;

use App\ClientRequest;
use App\Core\Renderer;

class Controller
{
    public function locations(int $page = 1): string
    {
        $locations = (new ClientRequest("location/?page=$page"))->getLocations();
        $pages = (new ClientRequest("location/?page=$page"))->getCountOfPages();
        return (new Renderer($locations, $pages))->render('locations.twig');
    }

    public function episodes(int $page = 1): string
    {
        $episodes = (new ClientRequest("episode/?page=$page"))->getEpisodes();
        $pages = (new ClientRequest("episode/?page=$page"))->getCountOfPages();
        return (new Renderer($episodes, $pages))->render('episodes.twig');
    }

    public function characters(int $page = 1): string
    {
        $characters = (new ClientRequest("character/?page=$page"))->getCharacters();
        $pages = (new ClientRequest("character/?page=$page"))->getCountOfPages();
        return (new Renderer($characters, $pages))->render('characters.twig');
    }

    public function character(int $id = 1): string
    {
        $character = (new ClientRequest("character/$id"))->getCharacter();
        return (new Renderer($character, $pages = 1))->render('character.twig');
    }

}