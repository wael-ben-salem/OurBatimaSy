<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Terrain
 */
#[ORM\Table(name: 'terrain')]
#[ORM\Index(name: 'Id_projet', columns: ['Id_projet'])]
#[ORM\Index(name: 'Id_visite', columns: ['Id_visite'])]
#[ORM\Entity]
class Terrain
{
    /**
     * @var int
     */
    #[ORM\Column(name: 'Id_terrain', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $idTerrain;

    /**
     * @var string
     */
    #[ORM\Column(name: 'emplacement', type: 'string', length: 100, nullable: false)]
    private $emplacement;

    /**
     * @var string
     */
    #[ORM\Column(name: 'caracteristiques', type: 'text', length: 65535, nullable: false)]
    private $caracteristiques;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'superficie', type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private $superficie;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'detailsGeo', type: 'string', length: 100, nullable: true)]
    private $detailsgeo;

    /**
     * @var \Projet
     */
    #[ORM\JoinColumn(name: 'Id_projet', referencedColumnName: 'Id_projet')]
    #[ORM\ManyToOne(targetEntity: \Projet::class)]
    private $idProjet;

    /**
     * @var \Visite
     */
    #[ORM\JoinColumn(name: 'Id_visite', referencedColumnName: 'Id_visite')]
    #[ORM\ManyToOne(targetEntity: \Visite::class)]
    private $idVisite;

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
