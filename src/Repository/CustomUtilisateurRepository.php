<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\DBAL\Connection;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CustomUtilisateurRepository
{
    private Connection $connection;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(Connection $connection, UserPasswordHasherInterface $passwordHasher)
    {
        $this->connection = $connection;
        $this->passwordHasher = $passwordHasher;
    }

    public function createUser(Utilisateur $user, string $plainPassword): void
    {
        // Hash the password
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);

        // Insert the user using a direct SQL query
        $sql = 'INSERT INTO utilisateur (nom, prenom, email, telephone, role, adresse, mot_de_passe, statut, isConfirmed)
                VALUES (:nom, :prenom, :email, :telephone, :role, :adresse, :mot_de_passe, :statut, :isConfirmed)';

        $this->connection->executeStatement($sql, [
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'email' => $user->getEmail(),
            'telephone' => $user->getTelephone(),
            'role' => $user->getRole(),
            'adresse' => $user->getAdresse(),
            'mot_de_passe' => $hashedPassword,
            'statut' => $user->getStatut(),
            'isConfirmed' => $user->isconfirmed() ? 1 : 0,
        ]);

        // Get the ID of the newly inserted user
        $id = $this->connection->lastInsertId();
        $user->setId($id);
    }
}
