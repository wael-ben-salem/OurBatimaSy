<?php

namespace App\Repository;

use App\Entity\Reclamation;
use Doctrine\DBAL\Connection;

class CustomReclamationRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(): array
    {
        $sql = 'SELECT id, description, statut, date, Utilisateur_id 
                FROM reclamation';
        
        $stmt = $this->connection->prepare($sql);
        $result = $stmt->executeQuery();
        $data = $result->fetchAllAssociative();
        
        $reclamations = [];
        foreach ($data as $row) {
            $reclamation = new Reclamation();
            $reclamation->setId($row['id']);
            $reclamation->setDescription($row['description']);
            $reclamation->setStatut($row['statut']);
            $reclamation->setDate(new \DateTime($row['date']));
            // We don't set the id_Utilisateur field since we don't need it for the form
            
            $reclamations[] = $reclamation;
        }
        
        return $reclamations;
    }

    public function find(int $id): ?Reclamation
    {
        $sql = 'SELECT id, description, statut, date, Utilisateur_id 
                FROM reclamation 
                WHERE id = :id';
        
        $stmt = $this->connection->prepare($sql);
        $result = $stmt->executeQuery(['id' => $id]);
        $row = $result->fetchAssociative();
        
        if (!$row) {
            return null;
        }
        
        $reclamation = new Reclamation();
        $reclamation->setId($row['id']);
        $reclamation->setDescription($row['description']);
        $reclamation->setStatut($row['statut']);
        $reclamation->setDate(new \DateTime($row['date']));
        // We don't set the id_Utilisateur field since we don't need it for the form
        
        return $reclamation;
    }
}
