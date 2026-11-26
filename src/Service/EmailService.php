<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    public function __construct(
        private MailerInterface $mailer
    ) {
    }

    // Email de bienvenue
    public function sendWelcomeEmail(string $userEmail, string $pseudonyme): void
    {
        $email = (new Email())
            ->from('devdocs@example.com')
            ->to($userEmail)
            ->subject('Bienvenue sur DevDocs !')
            ->html("
                <h1>Bienvenue sur DevDocs, {$pseudonyme} ! 🎉</h1>
                <p>Votre compte a été créé avec succès.</p>
                <p>Vous pouvez maintenant partager vos connaissances,
                consulter des tutoriels et apprendre de nouvelles technologies.</p>
                <p><strong>L'équipe DevDocs</strong></p>
            ");

        $this->mailer->send($email);
    }

    // Notification à l'administrateur
    public function sendNewTutorialNotification(
        string $tutorialTitle,
        string $authorName
    ): void {
        $email = (new Email())
            ->from('devdocs@example.com')
            ->to('admin@example.com')
            ->subject('Nouveau tutoriel publié sur DevDocs')
            ->html("
                <h1>Nouveau tutoriel !</h1>
                <p>Un nouveau tutoriel a été créé sur DevDocs.</p>
                <p><strong>Titre :</strong> {$tutorialTitle}</p>
                <p><strong>Auteur :</strong> {$authorName}</p>
            ");

        $this->mailer->send($email);
    }
}