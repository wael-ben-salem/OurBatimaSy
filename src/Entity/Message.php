<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 */
#[ORM\Table(name: 'message')]
#[ORM\Index(name: 'conversation_id', columns: ['conversation_id'])]
#[ORM\Index(name: 'expediteur_id', columns: ['expediteur_id'])]
#[ORM\Entity]
class Message
{
    /**
     * @var int
     */
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $id;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'contenu', type: 'text', length: 65535, nullable: true)]
    private $contenu;

    /**
     * @var \DateTime|null
     */
    #[ORM\Column(name: 'date_envoi', type: 'datetime', nullable: true, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private $dateEnvoi = 'CURRENT_TIMESTAMP';

    /**
     * @var bool|null
     */
    #[ORM\Column(name: 'lu', type: 'boolean', nullable: true)]
    private $lu = '0';

    /**
     * @var \Conversation
     */
    #[ORM\JoinColumn(name: 'conversation_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: \Conversation::class)]
    private $conversation;

    /**
     * @var \Utilisateur
     */
    #[ORM\JoinColumn(name: 'expediteur_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: \Utilisateur::class)]
    private $expediteur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(?string $contenu): static
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getDateEnvoi(): ?\DateTimeInterface
    {
        return $this->dateEnvoi;
    }

    public function setDateEnvoi(?\DateTimeInterface $dateEnvoi): static
    {
        $this->dateEnvoi = $dateEnvoi;

        return $this;
    }

    public function isLu(): ?bool
    {
        return $this->lu;
    }

    public function setLu(?bool $lu): static
    {
        $this->lu = $lu;

        return $this;
    }

    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    public function setConversation(?Conversation $conversation): static
    {
        $this->conversation = $conversation;

        return $this;
    }

    public function getExpediteur(): ?Utilisateur
    {
        return $this->expediteur;
    }

    public function setExpediteur(?Utilisateur $expediteur): static
    {
        $this->expediteur = $expediteur;

        return $this;
    }


}
