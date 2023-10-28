<?php

namespace App\Controllers;

use App\ClientRequest;
use App\Core\Renderer;

//class Controller
//{
//    public function searchResults(int $page = 1): string
//    {
//        $searchName = $_POST['name'];
//        $searchStatus = $_POST['status'];
//        $searchGender = $_POST['gender'];
//        $uri = "character/?page=$page&name=$searchName&status=$searchStatus&gender=$searchGender";
//        $searchResults = (new ClientRequest($uri))->getSearchResults();
//        return (new Renderer())->renderPage('SearchResults.twig', $searchResults[0], $searchResults[1]);
//    }
//}