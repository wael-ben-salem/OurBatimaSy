<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Constructeur
 */
#[ORM\Table(name: 'constructeur')]
#[ORM\Entity]
class Constructeur
{
    /**
     * @var string
     */
    #[ORM\Column(name: 'specialite', type: 'string', length: 100, nullable: false)]
    private $specialite;

    /**
     * @var string
     */
    #[ORM\Column(name: 'salaire_heure', type: 'decimal', precision: 10, scale: 2, nullable: false)]
    private $salaireHeure;

    /**
     * @var \Utilisateur
     */
    #[ORM\JoinColumn(name: 'constructeur_id', referencedColumnName: 'id')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\OneToOne(targetEntity: \Utilisateur::class)]
    private $constructeur;

    public function getSpecialite(): ?string
    {
        return $this->specialite;
    }

    public function setSpecialite(string $specialite): static
    {
        $this->specialite = $specialite;

        return $this;
    }

    public function getSalaireHeure(): ?string
    {
        return $this->salaireHeure;
    }

    public function setSalaireHeure(string $salaireHeure): static
    {
        $this->salaireHeure = $salaireHeure;

        return $this;
    }

    public function getConstructeur(): ?Utilisateur
    {
        return $this->constructeur;
    }

    public function setConstructeur(?Utilisateur $constructeur): static
    {
        $this->constructeur = $constructeur;

        return $this;
    }


}
