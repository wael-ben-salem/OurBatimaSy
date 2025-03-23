<?php

namespace App\Entity;

use App\Repository\NoteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: NoteRepository::class)]
#[Groups(['planning:read'])] // Add this to the Note class annotation
class Note
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['note:read', 'note:details', 'planning:read'])] // Add 'planning:read' to ID
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['note:read', 'note:details', 'planning:read'])] // Add 'planning:read' to title
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['note:read', 'note:details', 'planning:read'])] // Add 'planning:read' to content
    private ?string $content = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['note:read', 'note:details'])] // No change needed
    private ?\DateTimeImmutable $date = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'notes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['note:details', 'planning:read'])] // 'planning:read' here
    private ?User $createdBy = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[Groups(['note:read', 'note:details', 'planning:read','notification:read'])] // 'planning:read' here
    private ?User $assignedTo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(?\DateTimeImmutable $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getAssignedTo(): ?User
    {
        return $this->assignedTo;
    }

    public function setAssignedTo(?User $assignedTo): static
    {
        $this->assignedTo = $assignedTo;
        return $this;
    }
}