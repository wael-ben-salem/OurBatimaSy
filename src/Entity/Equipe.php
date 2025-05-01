<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Equipe
 */
#[ORM\Table(name: 'equipe')]
#[ORM\Index(name: 'idx_constructeur', columns: ['constructeur_id'])]
#[ORM\Index(name: 'idx_gestionnaire', columns: ['gestionnairestock_id'])]
#[ORM\UniqueConstraint(name: 'nom', columns: ['nom'])]
#[ORM\Entity]
class Equipe
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
    #[ORM\Column(name: 'nom', type: 'string', length: 100, nullable: false)]
    private $nom;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'date_creation', type: 'date', nullable: false, options: ['default' => 'CURRENT_DATE'])]
    private $dateCreation = 'CURRENT_DATE';

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'rating', type: 'decimal', precision: 3, scale: 2, nullable: true, options: ['default' => '0.00'])]
    private $rating = '0.00';

    /**
     * @var \Constructeur
     */
    
#[ORM\JoinColumn(name: 'constructeur_id', referencedColumnName: 'constructeur_id')]
#[ORM\ManyToOne(targetEntity: \Constructeur::class , cascade: ["persist"])]


    private $constructeur;

    /**
     * @var \Gestionnairestock
     */
    #[ORM\JoinColumn(name: 'gestionnairestock_id', referencedColumnName: 'gestionnairestock_id')]
    #[ORM\ManyToOne(targetEntity: \Gestionnairestock::class , cascade: ["persist"])]
    private $gestionnairestock;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    #[ORM\JoinTable(name: 'equipe_artisan')]
    #[ORM\JoinColumn(name: 'equipe_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'artisan_id', referencedColumnName: 'artisan_id')]
    #[ORM\ManyToMany(targetEntity: \Artisan::class, inversedBy: 'equipe', cascade: ["persist"])]
    private $artisan = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->teamRooms = new ArrayCollection();

        $this->artisan = new \Doctrine\Common\Collections\ArrayCollection();
        $this->projets = new \Doctrine\Common\Collections\ArrayCollection(); // Ajoutez cette ligne

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

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getRating(): ?string
    {
        return $this->rating;
    }

    public function setRating(?string $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getConstructeur(): ?Constructeur
    {
        return $this->constructeur;
    }

    public function setConstructeur(?Constructeur $constructeur): static
    {
        $this->constructeur = $constructeur;

        return $this;
    }

    public function getGestionnairestock(): ?Gestionnairestock
    {
        return $this->gestionnairestock;
    }

    public function setGestionnairestock(?Gestionnairestock $gestionnairestock): static
    {
        $this->gestionnairestock = $gestionnairestock;

        return $this;
    }

    /**
     * @return Collection<int, Artisan>
     */
    public function getArtisan(): Collection
    {
        return $this->artisan;
    }

// Ajoutez cette méthode pour la relation ManyToMany
public function getArtisans(): Collection
{
    return $this->artisan;
}

public function addArtisan(Artisan $artisan): self
{
    if (!$this->artisan->contains($artisan)) {
        $this->artisan[] = $artisan;
    }

    return $this;
}

public function removeArtisan(Artisan $artisan): self
{
    $this->artisan->removeElement($artisan);

    return $this;
}

#[ORM\OneToMany(mappedBy: 'idEquipe', targetEntity: Projet::class)]
private Collection $projets;

/**
 * @return Collection<int, Projet>
 */
public function getProjets(): Collection
{
    return $this->projets;
}

public function addProjet(Projet $projet): self
{
    if (!$this->projets->contains($projet)) {
        $this->projets->add($projet);
        $projet->setIdEquipe($this);
    }

    return $this;
}

public function removeProjet(Projet $projet): self
{
    if ($this->projets->removeElement($projet)) {
        // set the owning side to null (unless already changed)
        if ($projet->getIdEquipe() === $this) {
            $projet->setIdEquipe(null);
        }
    }

    return $this;
}
#[ORM\OneToMany(targetEntity: TeamRoom::class, mappedBy: 'equipe')]
private $teamRooms;
public function getTeamRooms(): Collection
{
    return $this->teamRooms;
}



}


