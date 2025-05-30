<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class CollecteController extends AbstractController
{
    #[Route('/collecte', name: 'app_collecte')]
    public function index(HttpClientInterface $client, Request $request)
    {
        // Fetch data from the API
        $response = $client->request('GET', 'https://jsonplaceholder.typicode.com/posts');
        
        // Check if the response is successful
        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to fetch data from API');
        }

        // Decode the JSON response
        $data = $response->toArray();
        return $this->json($data);

        
    }
   

}
