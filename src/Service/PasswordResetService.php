<?php
// src/Service/PasswordResetService.php

namespace App\Service;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class PasswordResetService
{
    public function __construct(
        private EntityManagerInterface $em,
        private EmailService $emailService,
        private LoggerInterface $logger,
        private int $codeExpirationMinutes = 15,
        private int $maxAttempts = 3
    ) {}
// src/Service/PasswordResetService.php
public function requestReset(string $email): array
{
    $user = $this->em->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);
    
    if (!$user) {
        return ['success' => false, 'message' => 'Aucun compte associé à cet email'];
    }

    // Génération d'un nouveau code
    $code = (string) random_int(100000, 999999); // Garantit un code à 6 chiffres
    
    // Enregistrement avec flush explicite
    $user->setResetPasswordCode($code)
         ->setResetPasswordAttempts(0)
         ->setResetPasswordCodeSentAt(new \DateTime())
         ->setResetPasswordCodeExpiresAt(new \DateTime("+{$this->codeExpirationMinutes} minutes"));

    $this->em->persist($user);
    $this->em->flush();

    // Debug: vérifier que le code est bien enregistré
    $this->logger->info('Code généré pour {email}: {code}', [
        'email' => $email,
        'code' => $code,
        'db_code' => $user->getResetPasswordCode() // Vérification
    ]);

    // Envoi de l'email
    if (!$this->emailService->sendPasswordResetEmail($user->getEmail(), $user->getFullName(), $code)) {
        return ['success' => false, 'message' => 'Erreur lors de l\'envoi du code'];
    }

    return ['success' => true];
}
public function verifyCode(string $email, string $code): array
{
    $user = $this->em->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);
    
    if (!$user) {
        return ['success' => false, 'message' => 'Utilisateur non trouvé'];
    }

    // Vérifier les tentatives
    if ($user->getResetPasswordAttempts() >= $this->maxAttempts) {
        return ['success' => false, 'message' => 'Trop de tentatives. Veuillez réessayer plus tard.'];
    }

    // Vérifier l'expiration
    if ($user->getResetPasswordCodeExpiresAt() < new \DateTime()) {
        return ['success' => false, 'message' => 'Code expiré'];
    }

    // Vérifier le code
    if ($user->getResetPasswordCode() !== $code) {
        $user->setResetPasswordAttempts($user->getResetPasswordAttempts() + 1);
        $this->em->flush();

        return [
            'success' => false,
            'message' => 'Code incorrect',
            'remaining_attempts' => $this->maxAttempts - $user->getResetPasswordAttempts()
        ];
    }

    return ['success' => true];
}

    public function resendCode(string $email): array
    {
        return $this->requestReset($email);
    }
}