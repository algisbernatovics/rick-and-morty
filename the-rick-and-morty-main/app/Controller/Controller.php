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
        return (new Renderer())->renderPage('locations.twig', $locations, $pages);
    }

    public function episodes(int $page = 1): string
    {
        $episodes = (new ClientRequest("episode/?page=$page"))->getEpisodes();
        $pages = (new ClientRequest("episode/?page=$page"))->getCountOfPages();
        return (new Renderer())->renderPage('episodes.twig', $episodes, $pages);
    }

    public function characters(int $page = 1): string
    {
        $characters = (new ClientRequest("character/?page=$page"))->getCharacters();
        $pages = (new ClientRequest("character/?page=$page"))->getCountOfPages();
        return (new Renderer())->renderPage('characters.twig', $characters, $pages);
    }

    public function character(int $id = 1): string
    {
        $character = (new ClientRequest("character/$id"))->getCharacter();
        return (new Renderer())->renderSinglePage('character.twig', $character[0], $character[1]);
    }

    public function location(int $id = 1): string
    {
        $location = (new ClientRequest("location/$id"))->getLocation();
        return (new Renderer())->renderSinglePage('location.twig', $location[0], $location[1]);
    }

    public function episode(int $id = 1): string
    {
        $episode = (new ClientRequest("episode/$id"))->getEpisode();
        return (new Renderer())->renderSinglePage('episode.twig', $episode[0], $episode[1]);
    }

}