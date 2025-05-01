<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Tache
 */
#[ORM\Table(name: 'tache')]
#[ORM\Index(name: 'constructeur_id', columns: ['constructeur_id'])]
#[ORM\Index(name: 'artisan_id', columns: ['artisan_id'])]
#[ORM\Index(name: 'Id_projet', columns: ['Id_projet'])]
#[ORM\Entity]
class Tache
{
    /**
     * @var int
     */
    #[ORM\Column(name: 'id_tache', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $idTache;

    /**
     * @var string
     */
    #[ORM\Column(name: 'description', type: 'string', length: 255, nullable: false)]
    private $description;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'date_debut', type: 'date', nullable: false)]
    #[Assert\NotBlank(message: "La date de début est obligatoire")]
    #[Assert\Type("\DateTimeInterface", message: "La date doit être au format valide")]
    private $dateDebut;

    /**
     * @var \DateTime|null
     */
    #[ORM\Column(name: 'date_fin', type: 'date', nullable: true)]
    #[Assert\Type("\DateTimeInterface", message: "La date doit être au format valide")]
    #[Assert\GreaterThanOrEqual(
        propertyPath: "dateDebut",
        message: "La date de fin doit être postérieure ou égale à la date de début"
    )]
    private $dateFin;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'etat', type: 'string', length: 0, nullable: true)]
    private $etat;

    /**
     * @var \Constructeur
     */
    #[ORM\JoinColumn(name: 'constructeur_id', referencedColumnName: 'constructeur_id')]
    #[ORM\ManyToOne(targetEntity: \Constructeur::class)]
    private $constructeur;

    /**
     * @var \Artisan
     */
    #[ORM\JoinColumn(name: 'artisan_id', referencedColumnName: 'artisan_id')]
    #[ORM\ManyToOne(targetEntity: \Artisan::class)]
    private $artisan;

    /**
     * @var \Projet
     */
    #[ORM\JoinColumn(name: 'Id_projet', referencedColumnName: 'Id_projet')]
    #[ORM\ManyToOne(targetEntity: \Projet::class)]
    private $idProjet;

    public function getIdTache(): ?int
    {
        return $this->idTache;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): static
    {
        $this->etat = $etat;

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

    public function getArtisan(): ?Artisan
    {
        return $this->artisan;
    }

    public function setArtisan(?Artisan $artisan): static
    {
        $this->artisan = $artisan;

        return $this;
    }

    public function getIdProjet(): ?Projet
    {
        return $this->idProjet;
    }

    public function setIdProjet(?Projet $idProjet): static
    {
        $this->idProjet = $idProjet;

        return $this;
    }

    // In Tache entity (keep existing but add status)
    public function __toString(): string
    {
        $status = $this->etat ?? 'No status';
        return sprintf('Task#%d: %s [%s]',
            $this->idTache,
            $this->description ?? 'Unnamed',
            $status
        );
    }
}
