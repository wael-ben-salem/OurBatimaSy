<?php

// src/Entity/Discussion.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: 'App\Repository\DiscussionRepository')]
class Discussion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Plannification')]
    #[ORM\JoinColumn(name: 'plannification_id', referencedColumnName: 'id_plannification')]
    private $plannification;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Utilisateur')]
    private $sender;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Utilisateur')]
    private $recipient;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private $content;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    // Getters and Setters
    public function getId(): ?int { return $this->id; }
    public function getPlannification(): ?Plannification { return $this->plannification; }
    public function setPlannification(?Plannification $plannification): self { $this->plannification = $plannification; return $this; }
    public function getSender(): ?Utilisateur { return $this->sender; }
    public function setSender(?Utilisateur $sender): self { $this->sender = $sender; return $this; }
    public function getRecipient(): ?Utilisateur { return $this->recipient; }
    public function setRecipient(?Utilisateur $recipient): self { $this->recipient = $recipient; return $this; }
    public function getContent(): ?string { return $this->content; }
    public function setContent(string $content): self { $this->content = $content; return $this; }
    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $createdAt): self { $this->createdAt = $createdAt; return $this; }
}