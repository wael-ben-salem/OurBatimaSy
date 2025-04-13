<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Client
 */
#[ORM\Table(name: 'client')]
#[ORM\Entity]
class Client
{
    /**
     * @var \Utilisateur
     */
    #[ORM\JoinColumn(name: 'client_id', referencedColumnName: 'id')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\OneToOne(targetEntity: \Utilisateur::class)]
    private $client;

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
