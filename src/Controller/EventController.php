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
use App\Repository\UserRepository;


#[Route('/events')]
#[IsGranted('ROLE_USER')]
class EventController extends AbstractController
{
    #[Route('/', name: 'app_event_index', methods: ['GET'])]
    public function index(Request $request, EventRepository $eventRepository): Response
    {
        $startDate = $request->query->get('start_date');
        $endDate = $request->query->get('end_date');

        if ($startDate || $endDate) {
            $startDate = $startDate ? new \DateTime($startDate) : null;
            $endDate = $endDate ? new \DateTime($endDate) : null;
            $events = $eventRepository->findByDateRange($startDate, $endDate);
        } else {
            $events = $eventRepository->findAll();
        }

        return $this->render('event/index.html.twig', [
            'events' => $events,
            'start_date' => $startDate ? $startDate->format('Y-m-d') : '',
            'end_date' => $endDate ? $endDate->format('Y-m-d') : '',
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


    #[Route('/{id}/participants', name: 'app_event_participants', methods: ['GET'])]
    public function participants(Event $event): Response
    {
        // Vérifier que l'utilisateur a le droit d'accéder à cette page
        if (!$this->isGranted('ROLE_ADMIN') && $event->getCreator() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à gérer les participants');
        }

        return $this->render('event/participants.html.twig', [
            'event' => $event,
        ]);
    }



    #[Route('/{id}/remove-participant/{userId}', name: 'app_event_remove_participant', methods: ['POST'])]
    public function removeParticipant(Request $request, Event $event, int $userId, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        // Vérifier que l'utilisateur a le droit d'effectuer cette action
        if (!$this->isGranted('ROLE_ADMIN') && $event->getCreator() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à retirer des participants');
        }

        if ($this->isCsrfTokenValid('remove_participant'.$event->getId().$userId, $request->request->get('_token'))) {
            $participant = $userRepository->find($userId);

            if ($participant) {
                // Ne pas retirer le créateur de l'événement
                if ($participant !== $event->getCreator()) {
                    $event->removeParticipant($participant);
                    $entityManager->flush();
                    $this->addFlash('success', 'Le participant a été retiré avec succès.');
                } else {
                    $this->addFlash('error', 'Le créateur de l\'événement ne peut pas être retiré.');
                }
            }
        }

        return $this->redirectToRoute('app_event_participants', ['id' => $event->getId()]);
    }

}