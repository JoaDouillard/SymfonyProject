<?php

namespace App\Security;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class EmailVerifier
{
    public function __construct(
        private MailerInterface $mailer,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function sendEmailConfirmation(string $verifyEmailRouteName, UserInterface $user, TemplatedEmail $email): void
    {
        // Cette méthode ne fait rien pour l'instant car nous n'utilisons pas la vérification d'email
        // Vous pourrez l'implémenter plus tard si nécessaire
    }

    public function handleEmailConfirmation(Request $request, UserInterface $user): void
    {
        // Cette méthode ne fait rien pour l'instant car nous n'utilisons pas la vérification d'email
        // Vous pourrez l'implémenter plus tard si nécessaire
    }
}
