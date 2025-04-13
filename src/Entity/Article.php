<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Article
 */
#[ORM\Table(name: 'article')]
#[ORM\Index(name: 'fournisseur_id', columns: ['fournisseur_id'])]
#[ORM\Index(name: 'etapeprojet_id', columns: ['etapeprojet_id'])]
#[ORM\Index(name: 'stock_id', columns: ['stock_id'])]
#[ORM\Entity]
class Article
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
    #[ORM\Column(name: 'nom', type: 'string', length: 255, nullable: false)]
    private $nom;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'description', type: 'string', length: 500, nullable: true)]
    private $description;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'prix_unitaire', type: 'string', length: 50, nullable: true)]
    private $prixUnitaire;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'photo', type: 'string', length: 255, nullable: true)]
    private $photo;

    /**
     * @var \Etapeprojet
     */
    #[ORM\JoinColumn(name: 'etapeprojet_id', referencedColumnName: 'Id_etapeProjet')]
    #[ORM\ManyToOne(targetEntity: \Etapeprojet::class)]
    private $etapeprojet;

    /**
     * @var \Stock
     */
    #[ORM\JoinColumn(name: 'stock_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: \Stock::class)]
    private $stock;

    /**
     * @var \Fournisseur
     */
    #[ORM\JoinColumn(name: 'fournisseur_id', referencedColumnName: 'fournisseur_id')]
    #[ORM\ManyToOne(targetEntity: \Fournisseur::class)]
    private $fournisseur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrixUnitaire(): ?string
    {
        return $this->prixUnitaire;
    }

    public function setPrixUnitaire(?string $prixUnitaire): static
    {
        $this->prixUnitaire = $prixUnitaire;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getEtapeprojet(): ?Etapeprojet
    {
        return $this->etapeprojet;
    }

    public function setEtapeprojet(?Etapeprojet $etapeprojet): static
    {
        $this->etapeprojet = $etapeprojet;

        return $this;
    }

    public function getStock(): ?Stock
    {
        return $this->stock;
    }

    public function setStock(?Stock $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function getFournisseur(): ?Fournisseur
    {
        return $this->fournisseur;
    }

    public function setFournisseur(?Fournisseur $fournisseur): static
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }


}
