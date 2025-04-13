<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Projet
 */
#[ORM\Table(name: 'projet')]
#[ORM\Index(name: 'id_client', columns: ['id_client'])]
#[ORM\Index(name: 'Id_terrain', columns: ['Id_terrain'])]
#[ORM\Index(name: 'Id_equipe', columns: ['Id_equipe'])]
#[ORM\UniqueConstraint(name: 'nomProjet', columns: ['nomProjet'])]
#[ORM\Entity]
class Projet
{
    /**
     * @var int
     */
    #[ORM\Column(name: 'Id_projet', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $idProjet;

    /**
     * @var string
     */
    #[ORM\Column(name: 'type', type: 'string', length: 20, nullable: false)]
    private $type;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'styleArch', type: 'string', length: 20, nullable: true)]
    private $stylearch;

    /**
     * @var string
     */
    #[ORM\Column(name: 'budget', type: 'decimal', precision: 15, scale: 3, nullable: false)]
    private $budget;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'etat', type: 'string', length: 20, nullable: true)]
    private $etat;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'dateCreation', type: 'datetime', nullable: false, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private $datecreation = 'CURRENT_TIMESTAMP';

    /**
     * @var string
     */
    #[ORM\Column(name: 'nomProjet', type: 'string', length: 30, nullable: false)]
    private $nomprojet;

    /**
     * @var \Terrain
     */
    #[ORM\JoinColumn(name: 'Id_terrain', referencedColumnName: 'Id_terrain')]
    #[ORM\ManyToOne(targetEntity: \Terrain::class)]
    private $idTerrain;

    /**
     * @var \Equipe
     */
    #[ORM\JoinColumn(name: 'Id_equipe', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: \Equipe::class)]
    private $idEquipe;

    /**
     * @var \Client
     */
    #[ORM\JoinColumn(name: 'id_client', referencedColumnName: 'client_id')]
    #[ORM\ManyToOne(targetEntity: \Client::class)]
    private $idClient;

    public function getIdProjet(): ?int
    {
        return $this->idProjet;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getStylearch(): ?string
    {
        return $this->stylearch;
    }

    public function setStylearch(?string $stylearch): static
    {
        $this->stylearch = $stylearch;

        return $this;
    }

    public function getBudget(): ?string
    {
        return $this->budget;
    }

    public function setBudget(string $budget): static
    {
        $this->budget = $budget;

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

    public function getDatecreation(): ?\DateTimeInterface
    {
        return $this->datecreation;
    }

    public function setDatecreation(\DateTimeInterface $datecreation): static
    {
        $this->datecreation = $datecreation;

        return $this;
    }

    public function getNomprojet(): ?string
    {
        return $this->nomprojet;
    }

    public function setNomprojet(string $nomprojet): static
    {
        $this->nomprojet = $nomprojet;

        return $this;
    }

    public function getIdTerrain(): ?Terrain
    {
        return $this->idTerrain;
    }

    public function setIdTerrain(?Terrain $idTerrain): static
    {
        $this->idTerrain = $idTerrain;

        return $this;
    }

    public function getIdEquipe(): ?Equipe
    {
        return $this->idEquipe;
    }

    public function setIdEquipe(?Equipe $idEquipe): static
    {
        $this->idEquipe = $idEquipe;

        return $this;
    }

    public function getIdClient(): ?Client
    {
        return $this->idClient;
    }

    public function setIdClient(?Client $idClient): static
    {
        $this->idClient = $idClient;

        return $this;
    }


}
