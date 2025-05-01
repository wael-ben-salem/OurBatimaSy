<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Conversation
 */
#[ORM\Table(name: 'conversation')]
#[ORM\Index(name: 'equipe_id', columns: ['equipe_id'])]
#[ORM\Entity]
class Conversation
{
    /**
     * @var int
     */
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $id;

    /**
     * @var string
     */
    #[ORM\Column(name: 'nom', type: 'string', length: 255, nullable: false)]
    private $nom;

    /**
     * @var \DateTime|null
     */
    #[ORM\Column(name: 'date_creation', type: 'datetime', nullable: true, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private $dateCreation = 'CURRENT_TIMESTAMP';

    /**
     * @var \Equipe
     */
    #[ORM\JoinColumn(name: 'equipe_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: \Equipe::class)]
    private $equipe;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    #[ORM\ManyToMany(targetEntity: \Utilisateur::class, inversedBy: 'conversations')]
#[ORM\JoinTable(name: 'conversation_membre',
    joinColumns: [new ORM\JoinColumn(name: 'conversation_id', referencedColumnName: 'id')],
    inverseJoinColumns: [new ORM\JoinColumn(name: 'utilisateur_id', referencedColumnName: 'id')]
)]

    private $utilisateur = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->utilisateur = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getEquipe(): ?Equipe
    {
        return $this->equipe;
    }

    public function setEquipe(?Equipe $equipe): static
    {
        $this->equipe = $equipe;

        return $this;
    }

    /**
     * @return Collection<int, Utilisateur>
     */
    public function getUtilisateur(): Collection
    {
        return $this->utilisateur;
    }

    public function addUtilisateur(Utilisateur $utilisateur): static
    {
        if (!$this->utilisateur->contains($utilisateur)) {
            $this->utilisateur->add($utilisateur);
        }

        return $this;
    }

    public function removeUtilisateur(Utilisateur $utilisateur): static
    {
        $this->utilisateur->removeElement($utilisateur);

        return $this;
    }

}
