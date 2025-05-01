<?php

namespace App\Service;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Symfony\Component\Mime\Address;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;

use Psr\Log\LoggerInterface;

class EmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig,
        private LoggerInterface $logger,
        private string $senderEmail,
        private string $senderName = 'OurBatima'
    ) {}

    // src/Service/EmailService.php

public function sendConfirmationEmail(string $recipientEmail, string $username): bool
{
    try {
        $email = (new Email())
        ->from('ourbatima@gmail.com')
        ->to($recipientEmail)
        ->subject('Confirmation d\'inscription')
        ->html($this->twig->render('emails/welcome.html.twig', [
            'username' => $username,
            'signature' => 'L\'équipe OurBatima'
        ]))
        ->embedFromPath('public/img/logoemail.png', 'logo')
        ->embedFromPath('public/img/signa.png', 'signature'); // <-- Signature visuelle

        $this->mailer->send($email);
        return true;
    } catch (TransportExceptionInterface $e) {
        $this->logger->error('Échec envoi email confirmation', [
            'error' => $e->getMessage(),
            'email' => $recipientEmail
        ]);
        return false;
    }
}
// src/Service/EmailService.php

public function sendPasswordResetEmail(string $recipientEmail, string $username, string $resetCode): bool
{
    try {
        $email = (new TemplatedEmail())
            ->from(new Address($this->senderEmail, $this->senderName))
            ->to($recipientEmail)
            ->subject('Réinitialisation de votre mot de passe')
            ->htmlTemplate('emails/reset_password_emails.html.twig')
            ->context([
                'username' => $username,
                'code' => $resetCode,
                'expiration_delay' => '15 minutes'
            ])
            ->embedFromPath('public/img/logoemail.png', 'logo')
            ->embedFromPath('public/img/signa.png', 'signature');

        $this->mailer->send($email);
        return true;
    } catch (TransportExceptionInterface $e) {
        $this->logger->error('Échec envoi email réinitialisation', [
            'error' => $e->getMessage(),
            'email' => $recipientEmail
        ]);
        return false;
    }
}
}