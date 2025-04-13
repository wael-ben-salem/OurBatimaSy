<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Etapeprojet
 */
#[ORM\Table(name: 'etapeprojet')]
#[ORM\Index(name: 'Id_projet', columns: ['Id_projet'])]
#[ORM\Index(name: 'Id_rapport', columns: ['Id_rapport'])]
#[ORM\Entity]
class Etapeprojet
{
    /**
     * @var int
     */
    #[ORM\Column(name: 'Id_etapeProjet', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $idEtapeprojet;

    /**
     * @var string
     */
    #[ORM\Column(name: 'nomEtape', type: 'string', length: 50, nullable: false)]
    private $nometape;

    /**
     * @var string
     */
    #[ORM\Column(name: 'description', type: 'text', length: 65535, nullable: false)]
    private $description;

    /**
     * @var \DateTime|null
     */
    #[ORM\Column(name: 'dateDebut', type: 'date', nullable: true)]
    private $datedebut;

    /**
     * @var \DateTime|null
     */
    #[ORM\Column(name: 'dateFin', type: 'date', nullable: true)]
    private $datefin;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'statut', type: 'string', length: 0, nullable: true, options: ['default' => 'En attente'])]
    private $statut = 'En attente';

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'montant', type: 'decimal', precision: 15, scale: 3, nullable: true)]
    private $montant;

    /**
     * @var \Projet
     */
    #[ORM\JoinColumn(name: 'Id_projet', referencedColumnName: 'Id_projet')]
    #[ORM\ManyToOne(targetEntity: \Projet::class)]
    private $idProjet;

    /**
     * @var \Rapport
     */
    #[ORM\JoinColumn(name: 'Id_rapport', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: \Rapport::class)]
    private $idRapport;

    public function getIdEtapeprojet(): ?int
    {
        return $this->idEtapeprojet;
    }

    public function getNometape(): ?string
    {
        return $this->nometape;
    }

    public function setNometape(string $nometape): static
    {
        $this->nometape = $nometape;

        return $this;
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

    public function getDatedebut(): ?\DateTimeInterface
    {
        return $this->datedebut;
    }

    public function setDatedebut(?\DateTimeInterface $datedebut): static
    {
        $this->datedebut = $datedebut;

        return $this;
    }

    public function getDatefin(): ?\DateTimeInterface
    {
        return $this->datefin;
    }

    public function setDatefin(?\DateTimeInterface $datefin): static
    {
        $this->datefin = $datefin;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(?string $montant): static
    {
        $this->montant = $montant;

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

    public function getIdRapport(): ?Rapport
    {
        return $this->idRapport;
    }

    public function setIdRapport(?Rapport $idRapport): static
    {
        $this->idRapport = $idRapport;

        return $this;
    }


}
