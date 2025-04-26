<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Projet;
use App\Entity\Visite;

/**
 * Terrain
 */
#[ORM\Table(name: 'terrain')]
#[ORM\Index(name: 'Id_projet', columns: ['Id_projet'])]
#[ORM\Index(name: 'Id_visite', columns: ['Id_visite'])]
#[ORM\Entity]
class Terrain
{
    #[ORM\Column(name: 'Id_terrain', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $idTerrain;

    #[ORM\Column(name: 'emplacement', type: 'string', length: 100, nullable: false)]
    private $emplacement;

    #[ORM\Column(name: 'caracteristiques', type: 'text', length: 65535, nullable: false)]
    private $caracteristiques;

    #[ORM\Column(name: 'superficie', type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private $superficie;

    #[ORM\Column(name: 'detailsGeo', type: 'string', length: 100, nullable: true)]
    private $detailsgeo;

    #[ORM\JoinColumn(name: 'Id_projet', referencedColumnName: 'Id_projet', nullable: true)]
    #[ORM\ManyToOne(targetEntity: Projet::class)]
    private ?Projet $idProjet = null;

    #[ORM\JoinColumn(name: 'Id_visite', referencedColumnName: 'Id_visite', nullable: true)]
    #[ORM\ManyToOne(targetEntity: Visite::class)]
    private ?Visite $idVisite = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $longitude = null;

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }
    public function setLatitude(?float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }
    public function setLongitude(?float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }


    public function getIdTerrain(): ?int
    {
        return $this->idTerrain;
    }

    public function getEmplacement(): ?string
    {
        return $this->emplacement;
    }

    public function setEmplacement(string $emplacement): static
    {
        $this->emplacement = $emplacement;

        return $this;
    }

    public function getCaracteristiques(): ?string
    {
        return $this->caracteristiques;
    }

    public function setCaracteristiques(string $caracteristiques): static
    {
        $this->caracteristiques = $caracteristiques;

        return $this;
    }

    public function getSuperficie(): ?string
    {
        return $this->superficie;
    }

    public function setSuperficie(?string $superficie): static
    {
        $this->superficie = $superficie;

        return $this;
    }

    public function getDetailsgeo(): ?string
    {
        return $this->detailsgeo;
    }

    public function setDetailsgeo(?string $detailsgeo): static
    {
        $this->detailsgeo = $detailsgeo;

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

    public function getIdVisite(): ?Visite
    {
        return $this->idVisite;
    }

    public function setIdVisite(?Visite $idVisite): static
    {
        $this->idVisite = $idVisite;

        return $this;
    }
}
