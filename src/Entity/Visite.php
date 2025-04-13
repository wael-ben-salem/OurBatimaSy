<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Visite
 */
#[ORM\Table(name: 'visite')]
#[ORM\Index(name: 'Id_terrain', columns: ['Id_terrain'])]
#[ORM\Index(name: 'Id_projet', columns: ['Id_projet'])]
#[ORM\Entity]
class Visite
{
    /**
     * @var int
     */
    #[ORM\Column(name: 'Id_visite', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $idVisite;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'dateVisite', type: 'date', nullable: false)]
    private $datevisite;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'observations', type: 'string', length: 200, nullable: true)]
    private $observations;

    /**
     * @var \Projet
     */
    #[ORM\JoinColumn(name: 'Id_projet', referencedColumnName: 'Id_projet')]
    #[ORM\ManyToOne(targetEntity: \Projet::class)]
    private $idProjet;

    /**
     * @var \Terrain
     */
    #[ORM\JoinColumn(name: 'Id_terrain', referencedColumnName: 'Id_terrain')]
    #[ORM\ManyToOne(targetEntity: \Terrain::class)]
    private $idTerrain;

    public function getIdVisite(): ?int
    {
        return $this->idVisite;
    }

    public function getDatevisite(): ?\DateTimeInterface
    {
        return $this->datevisite;
    }

    public function setDatevisite(\DateTimeInterface $datevisite): static
    {
        $this->datevisite = $datevisite;

        return $this;
    }

    public function getObservations(): ?string
    {
        return $this->observations;
    }

    public function setObservations(?string $observations): static
    {
        $this->observations = $observations;

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

    public function getIdTerrain(): ?Terrain
    {
        return $this->idTerrain;
    }

    public function setIdTerrain(?Terrain $idTerrain): static
    {
        $this->idTerrain = $idTerrain;

        return $this;
    }


}
