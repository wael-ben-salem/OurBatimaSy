<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Teamrating
 */
#[ORM\Table(name: 'teamrating')]
#[ORM\Index(name: 'team_id', columns: ['team_id'])]
#[ORM\Index(name: 'client_id', columns: ['client_id'])]
#[ORM\Entity]
class Teamrating
{
    /**
     * @var int
     */
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $id;

    /**
     * @var float|null
     */
    #[ORM\Column(name: 'rating', type: 'float', precision: 10, scale: 0, nullable: true)]
    private $rating;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private $createdAt = 'CURRENT_TIMESTAMP';

    /**
     * @var \Utilisateur
     */
    #[ORM\JoinColumn(name: 'client_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: \Utilisateur::class)]
    private $client;

    /**
     * @var \Equipe
     */
    #[ORM\JoinColumn(name: 'team_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: \Equipe::class)]
    private $team;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(?float $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getClient(): ?Utilisateur
    {
        return $this->client;
    }

    public function setClient(?Utilisateur $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getTeam(): ?Equipe
    {
        return $this->team;
    }

    public function setTeam(?Equipe $team): static
    {
        $this->team = $team;

        return $this;
    }


}
