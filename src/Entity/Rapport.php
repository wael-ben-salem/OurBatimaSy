<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Rapport
 */
#[ORM\Table(name: 'rapport')]
#[ORM\Index(name: 'Id_etapeProjet', columns: ['Id_etapeProjet'])]
#[ORM\Entity]
class Rapport
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
    #[ORM\Column(name: 'titre', type: 'string', length: 100, nullable: false)]
    private $titre;

    /**
     * @var string
     */
    #[ORM\Column(name: 'contenu', type: 'text', length: 65535, nullable: false)]
    private $contenu;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'dateCreation', type: 'date', nullable: false)]
    private $datecreation;

    /**
     * @var \Etapeprojet
     */
    #[ORM\JoinColumn(name: 'Id_etapeProjet', referencedColumnName: 'Id_etapeProjet')]
    #[ORM\ManyToOne(targetEntity: \Etapeprojet::class)]
    private $idEtapeprojet;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;

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

    public function getIdEtapeprojet(): ?Etapeprojet
    {
        return $this->idEtapeprojet;
    }

    public function setIdEtapeprojet(?Etapeprojet $idEtapeprojet): static
    {
        $this->idEtapeprojet = $idEtapeprojet;

        return $this;
    }


}
