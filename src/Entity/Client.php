<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[ORM\Table(name: 'client')]
class Client
{
    #[ORM\Id]
    #[ORM\OneToOne(inversedBy: 'client', targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'client_id', referencedColumnName: 'id')]
    private ?Utilisateur $client = null;

    public function getClient(): ?Utilisateur
    {
        return $this->client;
    }

    public function setClient(?Utilisateur $client): static
    {
        $this->client = $client;

        return $this;
    }
    
}
