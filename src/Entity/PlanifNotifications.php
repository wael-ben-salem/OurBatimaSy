<?php

// src/Entity/PlanifNotifications.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Utilisateur;
use App\Entity\Plannification;

#[ORM\Entity(repositoryClass: 'App\Repository\PlanifNotificationsRepository')]
class PlanifNotifications
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'text')]
    private $message;

    #[ORM\Column(type: 'boolean')]
    private $isRead = false;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $recipient;

    #[ORM\ManyToOne(targetEntity: Plannification::class)]
    #[ORM\JoinColumn(name: 'plannification_id', referencedColumnName: 'id_plannification')]
    private $plannification;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // Getters and Setters
    public function getId(): ?int { return $this->id; }
    public function getMessage(): ?string { return $this->message; }
    public function setMessage(string $message): self { $this->message = $message; return $this; }
    public function getIsRead(): ?bool { return $this->isRead; }
    public function setIsRead(bool $isRead): self { $this->isRead = $isRead; return $this; }
    public function getRecipient(): ?Utilisateur { return $this->recipient; }
    public function setRecipient(?Utilisateur $recipient): self { $this->recipient = $recipient; return $this; }
    public function getPlannification(): ?Plannification { return $this->plannification; }
    public function setPlannification(?Plannification $plannification): self { $this->plannification = $plannification; return $this; }
    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $createdAt): self { $this->createdAt = $createdAt; return $this; }
}