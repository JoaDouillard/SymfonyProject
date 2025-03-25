<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\Event;
use App\Repository\ArtistRepository;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api', name: 'api_')]
final class ApiController extends AbstractController
{
    private EventRepository $eventRepository;
    private ArtistRepository $artistRepository;
    private SerializerInterface $serializer;

    public function __construct(
        EventRepository $eventRepository,
        ArtistRepository $artistRepository,
        SerializerInterface $serializer
    ) {
        $this->eventRepository = $eventRepository;
        $this->artistRepository = $artistRepository;
        $this->serializer = $serializer;
    }

    // ENDPOINTS POUR LES ÉVÉNEMENTS

    #[Route('/events', name: 'events_list', methods: ['GET'])]
    public function listEvents(): JsonResponse
    {
        $events = $this->eventRepository->findAll();
        return $this->json($events, Response::HTTP_OK, [], ['groups' => 'event:read']);
    }

    #[Route('/events/{id}', name: 'event_detail', methods: ['GET'])]
    public function detailEvent(int $id): JsonResponse
    {
        $event = $this->eventRepository->find($id);

        if (!$event) {
            return $this->json(['message' => 'Événement non trouvé'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($event, Response::HTTP_OK, [], ['groups' => ['event:read', 'event:item:read']]);
    }

    // ENDPOINTS POUR LES ARTISTES

    #[Route('/artists', name: 'artists_list', methods: ['GET'])]
    public function listArtists(ArtistRepository $artistRepository): Response
    {
        $artists = $artistRepository->findAll();

        return $this->json($artists, 200, [], ['groups' => 'artist:read']);
    }

    #[Route('/artists/{id}', name: 'artist_detail', methods: ['GET'])]
    public function detailArtist(Artist $artist): Response
    {
        // Ici, nous ajoutons le groupe artist:item:read pour inclure les événements associés
        return $this->json($artist, 200, [], ['groups' => ['artist:read', 'artist:item:read']]);
    }

    // Nouvelle méthode pour obtenir les événements d'un artiste spécifique
    #[Route('/artists/{id}/events', name: 'artist_events', methods: ['GET'])]
    public function getArtistEvents(int $id): JsonResponse
    {
        $artist = $this->artistRepository->find($id);

        if (!$artist) {
            return $this->json(['message' => 'Artiste non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $events = $artist->getEvents();

        return $this->json($events, Response::HTTP_OK, [], ['groups' => ['event:read']]);
    }
}
