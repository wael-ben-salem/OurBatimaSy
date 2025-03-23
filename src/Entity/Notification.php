<?php

// src/Entity/Notification.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['notification:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['notification:read'])]
    private string $message;

    #[ORM\Column]
    #[Groups(['notification:read'])]
    private bool $isRead = false;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $recipient = null;

    #[ORM\ManyToOne(targetEntity: Planning::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['notification:read'])]
    private ?Planning $planning = null;

    #[ORM\Column]
    #[Groups(['notification:read'])]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // Getters and setters
    public function getId(): ?int { return $this->id; }
    public function getMessage(): string { return $this->message; }
    public function setMessage(string $message): self { $this->message = $message; return $this; }
    public function isRead(): bool { return $this->isRead; }
    public function markAsRead(): self { $this->isRead = true; return $this; }
    public function getRecipient(): ?User { return $this->recipient; }
    public function setRecipient(?User $recipient): self { $this->recipient = $recipient; return $this; }
    public function getPlanning(): ?Planning { return $this->planning; }
    public function setPlanning(?Planning $planning): self { $this->planning = $planning; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}