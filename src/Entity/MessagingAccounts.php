<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * MessagingAccounts
 */
#[ORM\Table(name: 'messaging_accounts')]
#[ORM\Index(name: 'idx_role_specific', columns: ['role_specific_id'])]
#[ORM\Entity]
class MessagingAccounts
{
    /**
     * @var int|null
     */
    #[ORM\Column(name: 'role_specific_id', type: 'integer', nullable: true, options: ['comment' => 'ID spécifique au rôle (artisan_id, constructeur_id, etc.)'])]
    private $roleSpecificId;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private $createdAt = 'CURRENT_TIMESTAMP';

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'username', type: 'string', length: 255, nullable: true)]
    private $username;
                        
    /**
     * @var \Utilisateur
     */
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\OneToOne(targetEntity: \Utilisateur::class)]
    private $user;

    public function getRoleSpecificId(): ?int
    {
        return $this->roleSpecificId;
    }

    public function setRoleSpecificId(?int $roleSpecificId): static
    {
        $this->roleSpecificId = $roleSpecificId;

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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getUser(): ?Utilisateur
    {
        return $this->user;
    }

    public function setUser(?Utilisateur $user): static
    {
        $this->user = $user;

        return $this;
    }
  

}
