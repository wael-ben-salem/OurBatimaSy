<?php

// src/Entity/Message.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: App\Repository\MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['message:read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    #[Groups(['message:read'])]
    private string $content;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['message:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['message:read'])]
    private User $sender;

    #[ORM\ManyToOne(targetEntity: Planning::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['message:read'])]
    private Planning $planning;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // Getters and Setters
    public function getId(): ?int { return $this->id; }
    public function getContent(): string { return $this->content; }
    public function setContent(string $content): self { $this->content = $content; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getSender(): User { return $this->sender; }
    public function setSender(User $sender): self { $this->sender = $sender; return $this; }
    public function getPlanning(): Planning { return $this->planning; }
    public function setPlanning(Planning $planning): self { $this->planning = $planning; return $this; }
}