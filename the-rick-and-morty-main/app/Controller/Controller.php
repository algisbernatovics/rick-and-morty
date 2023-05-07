<?php

namespace App\Controller;

use App\ClientRequest;
use App\Core\Renderer;

class Controller
{
    public function locations($page = 1): string
    {
        $locations = (new ClientRequest("location/?page=$page"))->getLocations();
        return (new Renderer($locations))->render('locations.twig');
    }

    public function episodes($page = 1): string
    {
        $episodes = (new ClientRequest("episode/?page=$page"))->getEpisodes();
        return (new Renderer($episodes))->render('episodes.twig');
    }

    public function characters($page = 1): string
    {
        $characters = (new ClientRequest("character/?page=$page"))->getCharacters();
        return (new Renderer($characters))->render('characters.twig');
    }

    public function character($id = 1): string
    {
        $character = (new ClientRequest("character/$id"))->getCharacter();
        return (new Renderer($character))->render('character.twig');
    }

}