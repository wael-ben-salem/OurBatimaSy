<?php
// src/Entity/TeamRoom.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

#[ORM\Entity]
#[ORM\Table(name: 'team_room')]
class TeamRoom
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Equipe::class, inversedBy: 'teamRooms')]
    #[ORM\JoinColumn(nullable: false)]
    private $equipe;

    #[ORM\Column(type: 'string', length: 100)]
    private $name;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    // Propriété messages (relation OneToMany avec TeamMessage)
    #[ORM\OneToMany(targetEntity: TeamMessage::class, mappedBy: 'room')]
    private $messages;

    #[ORM\OneToMany(targetEntity: TeamMember::class, mappedBy: 'room', cascade: ['persist', 'remove'])]
    private $members;

    // Propriété lastActivity (datetime nullable)
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $lastActivity = null;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->members = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    // Getters/Setters

    public function getId(): ?int 
    {
        return $this->id;
    }

    public function getEquipe(): ?Equipe 
    {
        return $this->equipe;
    }

    // src/Entity/TeamRoom.php

public function unreadCountForUser(Utilisateur $user): int
{
    $count = 0;
    foreach ($this->messages as $message) {
        if ($message->getSender() !== $user && !$message->isRead()) {
            $count++;
        }
    }
    return $count;
}
// src/Entity/TeamRoom.php

public function isGeneral(): bool
{
    return str_contains($this->name, 'Discussion Générale');
}
public function getOrderedMessages(): Collection
{
    $criteria = Criteria::create()
        ->orderBy(['sentAt' => 'DESC']);

    return $this->messages->matching($criteria);
}

    public function setEquipe(?Equipe $equipe): self 
    {
        $this->equipe = $equipe;
        return $this;
    }

    public function getName(): ?string 
    {
        return $this->name;
    }

    public function setName(string $name): self 
    {
        $this->name = $name;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface 
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self 
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getMessages(): Collection 
    {
        return $this->messages;
    }

    public function getMembers(): Collection 
    {
        return $this->members;
    }

    public function addMember(TeamMember $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
            $member->setRoom($this);
        }
        return $this;
    }

    
    public function removeMember(TeamMember $member): self
    {
        if ($this->members->removeElement($member)) {
            if ($member->getRoom() === $this) {
                $member->setRoom(null);
            }
        }
        return $this;
    }

    public function hasMember(Utilisateur $user): bool
    {
        foreach ($this->members as $member) {
            if ($member->getUser() === $user) {
                return true;
            }
        }
        return false;
    }

    // Getter and setter for lastActivity
    public function getLastActivity(): ?\DateTimeInterface
    {
        return $this->lastActivity;
    }
    // In TeamRoom.php
public function getLastMessage(): ?TeamMessage
{
    if ($this->messages->count() === 0) {
        return null;
    }
    
    // Sort messages by sentAt descending and get the first one
    $iterator = $this->messages->getIterator();
    $iterator->uasort(fn($a, $b) => $b->getSentAt() <=> $a->getSentAt());
    
    return $iterator->current();
}

    public function setLastActivity(?\DateTimeInterface $lastActivity): self
    {
        $this->lastActivity = $lastActivity;
        return $this;
    }

    #[ORM\Column(type: 'boolean')]
private bool $isRead = false;

public function getIsRead(): bool
{
    return $this->isRead;
}

public function setIsRead(bool $isRead): self
{
    $this->isRead = $isRead;
    return $this;
}

}
