<?php

// src/Entity/Planning.php
namespace App\Entity;

use App\Repository\PlanningRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PlanningRepository::class)]
class Planning
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['planning:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Note::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['planning:read'])]
    private ?Note $note = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['planning:read'])]
    private ?\DateTimeInterface $date_planifie = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Groups(['planning:read'])]
    private ?\DateTimeInterface $heure_debut = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Groups(['planning:read'])]
    private ?\DateTimeInterface $heure_fin = null;

    #[ORM\Column(length: 20)]
    #[Groups(['planning:read'])]
    private ?string $statut = 'planifié';

    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'planning')]
    private Collection $messages;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    // Getters and Setters
    public function getId(): ?int { return $this->id; }
    public function getNote(): ?Note { return $this->note; }
    public function setNote(?Note $note): self { $this->note = $note; return $this; }
    public function getDatePlanifie(): ?\DateTimeInterface { return $this->date_planifie; }
    public function setDatePlanifie(\DateTimeInterface $date_planifie): self { $this->date_planifie = $date_planifie; return $this; }
    public function getHeureDebut(): ?\DateTimeInterface { return $this->heure_debut; }
    public function setHeureDebut(\DateTimeInterface $heure_debut): self { $this->heure_debut = $heure_debut; return $this; }
    public function getHeureFin(): ?\DateTimeInterface { return $this->heure_fin; }
    public function setHeureFin(\DateTimeInterface $heure_fin): self { $this->heure_fin = $heure_fin; return $this; }
    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(string $statut): self {
        if (!in_array($statut, ['planifié', 'en cours', 'terminé'])) {
            throw new \InvalidArgumentException("Invalid status");
        }
        $this->statut = $statut;
        return $this;
    }
    public function getMessages(): Collection
    {
        return $this->messages;
    }
}
