<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Abonnement
 */
#[ORM\Table(name: 'abonnement')]
#[ORM\Entity]
class Abonnement
{
    /**
     * @var int
     */
    #[ORM\Column(name: 'id_abonnement', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $idAbonnement;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'nom_abonnement', type: 'string', length: 255, nullable: true)]
    private $nomAbonnement;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'duree', type: 'string', length: 100, nullable: true)]
    private $duree;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'prix', type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private $prix;

    public function getIdAbonnement(): ?int
    {
        return $this->idAbonnement;
    }

    public function getNomAbonnement(): ?string
    {
        return $this->nomAbonnement;
    }

    public function setNomAbonnement(?string $nomAbonnement): static
    {
        $this->nomAbonnement = $nomAbonnement;

        return $this;
    }

    public function getDuree(): ?string
    {
        return $this->duree;
    }

    public function setDuree(?string $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(?string $prix): static
    {
        $this->prix = $prix;

        return $this;
    }


}
