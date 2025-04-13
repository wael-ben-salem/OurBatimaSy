<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Projet;
use App\Entity\Terrain;

#[ORM\Entity]
#[ORM\Table(name: 'visite')]
#[ORM\Index(name: 'Id_terrain', columns: ['Id_terrain'])]
#[ORM\Index(name: 'Id_projet', columns: ['Id_projet'])]
class Visite
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'Id_visite', type: 'integer', nullable: false)]
    private ?int $idVisite = null;

    #[ORM\Column(name: 'dateVisite', type: Types::DATE_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $datevisite = null;

    #[ORM\Column(name: 'observations', type: 'string', length: 200, nullable: true)]
    private ?string $observations = null;

    #[ORM\ManyToOne(targetEntity: Projet::class)]
    #[ORM\JoinColumn(name: 'Id_projet', referencedColumnName: 'Id_projet')]
    private ?Projet $idProjet = null;

    #[ORM\ManyToOne(targetEntity: Terrain::class)]
    #[ORM\JoinColumn(name: 'Id_terrain', referencedColumnName: 'Id_terrain')]
    private ?Terrain $idTerrain = null;

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
