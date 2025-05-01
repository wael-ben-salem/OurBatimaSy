<?php
// src/Entity/TeamMessage.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'team_message')]
class TeamMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;
    #[ORM\Column(type: 'boolean')]
    private bool $isRead = false;

    #[ORM\ManyToOne(targetEntity: TeamRoom::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private $room;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $sender;

    #[ORM\Column(type: 'text')]
    private $content;

    #[ORM\Column(type: 'datetime')]
    private $sentAt;

    public function __construct()
    {
        $this->sentAt = new \DateTime();
    }

    public function isRead(): bool
{
    return $this->isRead;
}

public function setIsRead(bool $isRead): self
{
    $this->isRead = $isRead;
    return $this;
}
    // Getters/Setters
    public function getId(): ?int { return $this->id; }
    public function getRoom(): ?TeamRoom { return $this->room; }
    public function setRoom(?TeamRoom $room): self { $this->room = $room; return $this; }
    public function getSender(): ?Utilisateur { return $this->sender; }
    public function setSender(?Utilisateur $sender): self { $this->sender = $sender; return $this; }
    public function getContent(): ?string { return $this->content; }
    public function setContent(string $content): self { $this->content = $content; return $this; }
    public function getSentAt(): ?\DateTimeInterface { return $this->sentAt; }
    public function setSentAt(\DateTimeInterface $sentAt): self { $this->sentAt = $sentAt; return $this; }
}