<?php

namespace App\Controllers;

use App\ClientRequest;
use App\Core\Renderer;

class Controller
{
    public function locations(int $page = 1): string
    {
        $locations = (new ClientRequest("location/?page=$page"))->getLocations();
        $pages = (new ClientRequest("location/?page=$page"))->getCountOfPages();
        return (new Renderer())->renderPage('Locations.twig', $locations, $pages);
    }

    public function episodes(int $page = 1): string
    {
        $episodes = (new ClientRequest("episode/?page=$page"))->getEpisodes();
        $pages = (new ClientRequest("episode/?page=$page"))->getCountOfPages();
        return (new Renderer())->renderPage('Episodes.twig', $episodes, $pages);
    }

    public function characters(int $page = 1): string
    {
        $characters = (new ClientRequest("character/?page=$page"))->getCharacters();
        $pages = (new ClientRequest("character/?page=$page"))->getCountOfPages();
        return (new Renderer())->renderPage('Characters.twig', $characters, $pages);
    }

    public function singleCharacter(int $id = 1): string
    {
        $character = (new ClientRequest("character/$id"))->getSingleCharacter();
        return (new Renderer())->renderSinglePage('SingleCharacter.twig', $character[0], $character[1]);
    }

    public function singleLocation(int $id = 1): string
    {
        $location = (new ClientRequest("location/$id"))->getSingleLocation();
        return (new Renderer())->renderSinglePage('SingleLocation.twig', $location[0], $location[1]);
    }

    public function singleEpisode(int $id = 1): string
    {
        $episode = (new ClientRequest("episode/$id"))->getSingleEpisode();
        return (new Renderer())->renderSinglePage('SingleEpisode.twig', $episode[0], $episode[1]);
    }

    public function searchPage(): void
    {
        (new Renderer())->viewSearch('SearchPage.twig');
    }

    public function searchResults(int $page = 1): string
    {
        $searchName = $_POST['name'];
        $searchStatus = $_POST['status'];
        $searchGender = $_POST['gender'];
        $uri = "character/?page=$page&name=$searchName&status=$searchStatus&gender=$searchGender";
        $searchResults = (new ClientRequest($uri))->getSearchResults();
        return (new Renderer())->renderPage('SearchResults.twig', $searchResults[0], $searchResults[1]);
    }

    public function error(): string
    {
        return (new Renderer())->error('Error.twig');
    }
}