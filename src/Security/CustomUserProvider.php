<?php

namespace App\Security;

use App\Entity\Utilisateur;
use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class CustomUserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $sql = 'SELECT id, nom, prenom, email, telephone, role, adresse, mot_de_passe, statut, isConfirmed
                FROM utilisateur
                WHERE email = :email';

        $stmt = $this->connection->prepare($sql);
        $result = $stmt->executeQuery(['email' => $identifier]);
        $userData = $result->fetchAssociative();

        if (!$userData) {
            throw new UserNotFoundException(sprintf('User "%s" not found.', $identifier));
        }

        return $this->createUserFromData($userData);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof Utilisateur) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return Utilisateur::class === $class || is_subclass_of($class, Utilisateur::class);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Utilisateur) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $sql = 'UPDATE utilisateur SET mot_de_passe = :password WHERE id = :id';
        $this->connection->executeStatement($sql, [
            'password' => $newHashedPassword,
            'id' => $user->getId(),
        ]);
    }

    private function createUserFromData(array $userData): Utilisateur
    {
        $user = new Utilisateur();
        $user->setId($userData['id']);
        $user->setNom($userData['nom']);
        $user->setPrenom($userData['prenom']);
        $user->setEmail($userData['email']);
        $user->setTelephone($userData['telephone']);
        $user->setRole($userData['role']);
        $user->setAdresse($userData['adresse']);
        $user->setPassword($userData['mot_de_passe']);
        $user->setStatut($userData['statut']);
        $user->setIsconfirmed($userData['isConfirmed']);

        // These fields don't exist in the database, so we set them to null
        $user->setResetToken(null);
        $user->setResetTokenExpiry(null);

        return $user;
    }
}
