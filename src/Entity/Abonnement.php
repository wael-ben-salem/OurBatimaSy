<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Abonnement
 */
#[ORM\Table(name: 'abonnement')]
#[ORM\Entity]
class Abonnement
{
    #[ORM\Column(name: 'id_abonnement', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $idAbonnement;

    #[ORM\Column(name: 'nom_abonnement', type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Le nom d'abonnement ne peut pas être vide.")]
    #[Assert\Regex(
        pattern: "/^[A-Za-z ]+$/",
        message: "Le nom d'abonnement ne doit contenir que des lettres (A-Z)."
    )]
    private $nomAbonnement;

    #[ORM\Column(name: 'duree', type: 'string', length: 100, nullable: true)]
    #[Assert\Regex(
        pattern: "/^[A-Za-z0-9 ]+$/",
        message: "La durée doit contenir uniquement des lettres et des chiffres."
    )]
    private $duree;

    #[ORM\Column(name: 'prix', type: 'decimal', precision: 10, scale: 2, nullable: true)]
    #[Assert\Positive(message: "Le prix doit être un nombre positif.")]
    #[Assert\Range(
        notInRangeMessage: "Le prix doit être compris entre {{ min }} et {{ max }}.",
        min: 5,
        max: 500
    )]
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

    #[ORM\OneToMany(mappedBy: 'abonnement', targetEntity: UserAbonnement::class)]
    private Collection $userAbonnements;
    
    public function __construct()
    {
        $this->userAbonnements = new ArrayCollection();
    }
    
    public function getUserAbonnements(): Collection
    {
        return $this->userAbonnements;
    }
    
    public function addUserAbonnement(UserAbonnement $userAbonnement): static
    {
        if (!$this->userAbonnements->contains($userAbonnement)) {
            $this->userAbonnements[] = $userAbonnement;
            $userAbonnement->setAbonnement($this);
        }
    
        return $this;
    }
    
    public function removeUserAbonnement(UserAbonnement $userAbonnement): static
    {
        if ($this->userAbonnements->removeElement($userAbonnement)) {
            if ($userAbonnement->getAbonnement() === $this) {
                $userAbonnement->setAbonnement(null);
            }
        }
    
        return $this;
    }







}
