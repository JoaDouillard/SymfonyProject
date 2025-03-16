<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Form\ArtistType;
use App\Repository\ArtistRepository;
use App\Service\ImageUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/artists')]
class ArtistController extends AbstractController
{
    private $imageUploadService;

    public function __construct(ImageUploadService $imageUploadService)
    {
        $this->imageUploadService = $imageUploadService;
    }

    #[Route('/', name: 'app_artist_index', methods: ['GET'])]
    public function index(Request $request, ArtistRepository $artistRepository): Response
    {
        $searchTerm = $request->query->get('search', '');

        if (!empty($searchTerm)) {
            $artists = $artistRepository->findByNameLike($searchTerm);
        } else {
            $artists = $artistRepository->findAll();
        }

        return $this->render('artist/index.html.twig', [
            'artists' => $artists,
            'searchTerm' => $searchTerm,
        ]);
    }


    #[Route('/new', name: 'app_artist_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $artist = new Artist();
        $form = $this->createForm(ArtistType::class, $artist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le fichier d'image uploadé
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                try {
                    // Utilisation du service pour uploader et convertir l'image en WEBP
                    $imageFileName = $this->imageUploadService->upload($imageFile, 'artists');

                    // Enregistrer le nom du fichier dans l'entité Artist
                    $artist->setImageFilename($imageFileName);
                } catch (\Exception $e) {
                    $this->addFlash('danger', 'Erreur lors du téléchargement de l\'image: ' . $e->getMessage());
                }
            }

            $entityManager->persist($artist);
            $entityManager->flush();

            $this->addFlash('success', 'L\'artiste a été créé avec succès');
            return $this->redirectToRoute('app_artist_show', ['id' => $artist->getId()]);
        }

        return $this->render('artist/new.html.twig', [
            'artist' => $artist,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_artist_show', methods: ['GET'])]
    public function show(Artist $artist): Response
    {
        return $this->render('artist/show.html.twig', [
            'artist' => $artist,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_artist_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Artist $artist, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArtistType::class, $artist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                try {
                    // Supprimer l'ancienne image si elle existe
                    if ($artist->getImageFilename()) {
                        $oldImagePath = $this->getParameter('kernel.project_dir') . '/public/uploads/images/' . $artist->getImageFilename();
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }

                    // Utilisation du service pour uploader et convertir l'image en WEBP
                    $imageFileName = $this->imageUploadService->upload($imageFile, 'artists');

                    // Enregistrer le nom du fichier dans l'entité Artist
                    $artist->setImageFilename($imageFileName);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Erreur lors du téléchargement de l\'image: ' . $e->getMessage());
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'L\'artiste a été modifié avec succès');
            return $this->redirectToRoute('app_artist_show', ['id' => $artist->getId()]);
        }

        return $this->render('artist/edit.html.twig', [
            'artist' => $artist,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_artist_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Artist $artist, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$artist->getId(), $request->request->get('_token'))) {
            try {
                // Récupérer et supprimer tous les événements liés à cet artiste
                $events = $artist->getEvents();
                foreach ($events as $event) {
                    $entityManager->remove($event);
                }

                // Supprimer l'image de l'artiste si elle existe
                if ($artist->getImageFilename()) {
                    $imagePath = $this->getParameter('kernel.project_dir') . '/public/uploads/images/' . $artist->getImageFilename();
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }

                // Supprimer l'artiste
                $entityManager->remove($artist);
                $entityManager->flush();

                $this->addFlash('success', 'L\'artiste a été supprimé avec tous ses événements associés');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Une erreur est survenue lors de la suppression: ' . $e->getMessage());
            }
        } else {
            $this->addFlash('danger', 'Token CSRF invalide');
        }

        return $this->redirectToRoute('app_artist_index');
    }

}

