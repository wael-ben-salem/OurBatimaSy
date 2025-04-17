<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Contrat
 */
#[ORM\Table(name: 'contrat')]
#[ORM\Index(name: 'client_id', columns: ['client_id'])]
#[ORM\Index(name: 'Id_projet', columns: ['Id_projet'])]
#[ORM\Index(name: 'constructeur_id', columns: ['constructeur_id'])]
#[ORM\Entity]
class Contrat
{
    /**
     * @var int
     */
    #[ORM\Column(name: 'id_contrat', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $idContrat;

    /**
     * @var string
     */
    #[ORM\Column(name: 'type_contrat', type: 'string', length: 255, nullable: false)]
    private $typeContrat;

    /**
     * @var \DateTime|null
     */
    #[ORM\Column(name: 'date_signature', type: 'date', nullable: true)]
    private $dateSignature;

    /**
     * @var \DateTime|null
     */
    #[ORM\Column(name: 'date_debut', type: 'date', nullable: true)]
    private $dateDebut;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'signature_electronique', type: 'string', length: 500, nullable: true)]
    private $signatureElectronique;
       
    /**
     * @var \DateTime|null
     */
    #[ORM\Column(name: 'date_fin', type: 'date', nullable: true)]
    private $dateFin;

    /**
     * @var int|null
     */
    #[ORM\Column(name: 'montant_total', type: 'integer', nullable: true)]
    private $montantTotal;

    /**
     * @var \Constructeur
     */
    #[ORM\JoinColumn(name: 'constructeur_id', referencedColumnName: 'constructeur_id')]
    #[ORM\ManyToOne(targetEntity: \Constructeur::class)]
    private $constructeur;

    /**
     * @var \Client
     */
    #[ORM\JoinColumn(name: 'client_id', referencedColumnName: 'client_id')]
    #[ORM\ManyToOne(targetEntity: \Client::class)]
    private $client;

    /**
     * @var \Projet
     */
    #[ORM\JoinColumn(name: 'Id_projet', referencedColumnName: 'Id_projet')]
    #[ORM\ManyToOne(targetEntity: \Projet::class)]
    private $idProjet;

    public function getIdContrat(): ?int
    {
        return $this->idContrat;
    }

    public function getTypeContrat(): ?string
    {
        return $this->typeContrat;
    }

    public function setTypeContrat(string $typeContrat): static
    {
        $this->typeContrat = $typeContrat;

        return $this;
    }

    public function getDateSignature(): ?\DateTimeInterface
    {
        return $this->dateSignature;
    }

    public function setDateSignature(?\DateTimeInterface $dateSignature): static
    {
        $this->dateSignature = $dateSignature;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getSignatureElectronique(): ?string
    {
        return $this->signatureElectronique;
    }

    public function setSignatureElectronique(?string $signatureElectronique): static
    {
        $this->signatureElectronique = $signatureElectronique;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getMontantTotal(): ?int
    {
        return $this->montantTotal;
    }

    public function setMontantTotal(?int $montantTotal): static
    {
        $this->montantTotal = $montantTotal;

        return $this;
    }

    public function getConstructeur(): ?Constructeur
    {
        return $this->constructeur;
    }

    public function setConstructeur(?Constructeur $constructeur): static
    {
        $this->constructeur = $constructeur;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

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


}
