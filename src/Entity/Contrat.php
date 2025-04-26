<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity]
#[Vich\Uploadable]
class Contrat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_contrat', type: 'integer')]
    private ?int $idContrat = null;

    #[ORM\Column(name: 'type_contrat', type: 'string', length: 255)]
        #[Assert\Choice(
        choices: ['client', 'constructeur'],
        message: "Le type de contrat doit être soit 'client' soit 'constructeur'"
    )]
    #[Assert\NotNull(message: " le type de contrat est obligatoire ")]

    private ?string $typeContrat = null;

    #[ORM\Column(name: 'date_signature', type: 'date')]
    #[Assert\NotNull(message: "La date de signature est obligatoire")]
    private ?\DateTimeInterface $dateSignature = null;

    #[ORM\Column(name: 'date_debut', type: 'date')]
    #[Assert\NotNull(message: "La date de début est obligatoire")]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(name: 'signature_electronique', type: 'string', length: 500, nullable: true)]

    private ?string $signatureElectronique = null;

    #[ORM\Column(name: 'date_fin', type: 'date')]
    #[Assert\NotNull(message: "La date de fin est obligatoire")]
    private ?\DateTimeInterface $dateFin = null;
    
    
    

    #[ORM\Column(name: 'montant_total', type: 'integer')]
    #[Assert\NotBlank(message: "Le montant total est obligatoire")]
   
    #[Assert\LessThanOrEqual(
        value: 1000000,
        message: "Le montant ne peut pas dépasser 1,000,000"
    )]
    private ?int $montantTotal = null;

    #[ORM\ManyToOne(targetEntity: Projet::class)]
    #[ORM\JoinColumn(name: 'Id_projet', referencedColumnName: 'Id_projet', nullable: false)]
    #[Assert\NotNull(message: "Un projet doit être associé")]
    private ?Projet $idProjet = null;

    #[Vich\UploadableField(mapping: 'signature_images', fileNameProperty: 'signatureElectronique')]
    #[Assert\File(
        maxSize: "2M",
        mimeTypes: ["image/jpeg", "image/png"],
        mimeTypesMessage: "Seules les images JPEG et PNG sont acceptées",
        maxSizeMessage: "La taille maximale est de 2MB"
    )]
    private ?File $signatureFile = null;

    public function __construct()
    {
        $this->dateSignature = new \DateTime();
    }


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

    public function setDateSignature(\DateTimeInterface $dateSignature): static
    {
        $this->dateSignature = $dateSignature;
        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): static
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

    public function setDateFin(\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;
        return $this;
    }

    public function getMontantTotal(): ?int
    {
        return $this->montantTotal;
    }

    public function setMontantTotal(int $montantTotal): static
    {
        $this->montantTotal = $montantTotal;
        return $this;
    }

    public function getIdProjet(): ?Projet
    {
        return $this->idProjet;
    }

    public function setIdProjet(Projet $idProjet): static
    {
        $this->idProjet = $idProjet;
        return $this;
    }

    public function setSignatureFile(?File $file = null): void
    {
        $this->signatureFile = $file;
        if ($file) {
            $this->dateSignature = new \DateTimeImmutable();
        }
        
    }

    public function getSignatureFile(): ?File
    {
        return $this->signatureFile;
    }

    public function getSignaturePath(): ?string
    {
        return $this->signatureElectronique 
            ? '/signatures/'.$this->signatureElectronique 
            : null;
    }


    public function __toString(): string
    {
        return $this->typeContrat ?? 'Nouveau Contrat';
    }


    
}