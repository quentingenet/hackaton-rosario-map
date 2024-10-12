<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Component\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Component\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Component\HttpClient\HttpClient;

class MapController extends AbstractController
{
    #[Route('/map', name: 'home')]
    public function index(): Response
    {
        try {
            $client = HttpClient::create();
            $response = $client->request('GET', 'https://api.rosario.app/parishes-map');

            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Erreur lors de la récupération des données de l\'API : ' . $response->getStatusCode());
            }

            $points = $response->toArray();
            
            if (!isset($points['data'])) {
                throw new \Exception('Données reçues invalides.');
            }

            return $this->render('map/index.html.twig', [
                'points' => $points['data'],
            ]);
        } catch (TransportExceptionInterface | ClientExceptionInterface | ServerExceptionInterface $e) {
            return new Response('Erreur de communication avec le serveur : ' . $e->getMessage(), 500);
        } catch (\Exception $e) {
            return new Response('Une erreur est survenue : ' . $e->getMessage(), 500);
        }
    }
}