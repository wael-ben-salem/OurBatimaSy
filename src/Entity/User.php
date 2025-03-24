<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['note:details', 'user:list'])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(['note:details', 'user:list', 'planning:read'])]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(targetEntity: Note::class, mappedBy: 'createdBy')]
    #[Ignore]
    private Collection $notes;

    #[ORM\ManyToMany(targetEntity: Planning::class)]
    #[ORM\JoinTable(name: 'user_saved_plannings')]
    #[Groups(['user:saved'])] // Changed from 'planning:read'
    private Collection $savedPlannings;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
        $this->savedPlannings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void {}

    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function getSavedPlannings(): Collection
    {
        return $this->savedPlannings;
    }

    public function addSavedPlanning(Planning $planning): self
    {
        if (!$this->savedPlannings->contains($planning)) {
            $this->savedPlannings->add($planning);
        }
        return $this;
    }

    public function removeSavedPlanning(Planning $planning): self
    {
        $this->savedPlannings->removeElement($planning);
        return $this;
    }

    public function __toString(): string
    {
        return $this->email;
    }
}
