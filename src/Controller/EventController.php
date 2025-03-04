<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/events')]
class EventController extends AbstractController
{
    #[Route('/', name: 'app_event_index', methods: ['GET'])]
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('event/index.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $user = $this->getUser();

        // Définir l'utilisateur courant comme créateur
        $event->setCreator($user);

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Ajouter le créateur comme participant
            $event->addParticipant($user);

            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'L\'événement a été créé avec succès. Vous y participez automatiquement.');
            return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/mes-evenements', name: 'app_my_events', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function myEvents(EventRepository $eventRepository): Response
    {
        $user = $this->getUser();

        // Récupérer les événements où l'utilisateur est participant
        $participatingEvents = $eventRepository->findParticipatingEvents($user);

        // Récupérer les événements créés par l'utilisateur
        $createdEvents = $eventRepository->findBy(['creator' => $user]);

        return $this->render('event/my_events.html.twig', [
            'participating_events' => $participatingEvents,
            'created_events' => $createdEvents,
        ]);
    }

    #[Route('/{id}', name: 'app_event_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }
    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        // Seul le créateur de l'événement ou un admin peut éditer
        if (!$this->isGranted('ROLE_ADMIN') && $event->getCreator() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cet événement');
        }

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'L\'événement a été modifié avec succès');
            return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        // Seul le créateur de l'événement ou un admin peut supprimer
        if (!$this->isGranted('ROLE_ADMIN') && $event->getCreator() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer cet événement');
        }

        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();

            $this->addFlash('success', 'L\'événement a été supprimé');
        }

        return $this->redirectToRoute('app_event_index');
    }

    #[Route('/{id}/participate', name: 'app_event_participate', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function participate(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('participate'.$event->getId(), $request->request->get('_token'))) {
            $user = $this->getUser();
            if ($event->getParticipants()->contains($user)) {
                $event->removeParticipant($user);
                $message = 'Vous ne participez plus à cet événement';
            } else {
                $event->addParticipant($user);
                $message = 'Vous participez maintenant à cet événement';
            }

            $entityManager->flush();
            $this->addFlash('success', $message);
        }

        return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
    }

}
