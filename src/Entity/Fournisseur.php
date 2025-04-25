<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Fournisseur
 */
#[ORM\Table(name: 'fournisseur')]
#[ORM\UniqueConstraint(name: 'email', columns: ['email'])]
#[ORM\Entity]
class Fournisseur
{
    /**
     * @var int
     */
    #[ORM\Column(name: 'fournisseur_id', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $fournisseurId;

    /**
     * @var string
     */
    #[ORM\Column(name: 'nom', type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(message: 'The name cannot be blank.')]
    #[Assert\Length(
        min: 3,
        minMessage: 'The name must be at least {{ limit }} characters long.'
    )]
    #[Assert\Regex(
        pattern: '/^[^\d]*$/',
        message: 'The name cannot contain numbers.'
    )]
    private $nom;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'prenom', type: 'string', length: 255, nullable: true)]
    #[Assert\Length(
        min: 3,
        minMessage: 'The first name must be at least {{ limit }} characters long.'
    )]
    #[Assert\Regex(
        pattern: '/^[^\d]*$/',
        message: 'The first name cannot contain numbers.'
    )]
    private $prenom;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'email', type: 'string', length: 255, nullable: true)]
    #[Assert\Email(message: 'The email "{{ value }}" is not a valid email.')]
    private $email;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'numero_de_telephone', type: 'string', length: 50, nullable: true)]
    #[Assert\NotBlank(message: 'The phone number cannot be blank.')]
    #[Assert\Regex(
        pattern: '/^\d{8}$/',
        message: 'The phone number must be exactly 8 digits.'
    )]
    private $numeroDeTelephone;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'adresse', type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'The address cannot be blank.')]
    #[Assert\Length(
        min: 3,
        minMessage: 'The address must be at least {{ limit }} characters long.'
    )]
    private $adresse;

    public function getFournisseurId(): ?int
    {
        return $this->fournisseurId;
    }

    public function getId(): ?int
    {
        return $this->fournisseurId;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getNumeroDeTelephone(): ?string
    {
        return $this->numeroDeTelephone;
    }

    public function setNumeroDeTelephone(?string $numeroDeTelephone): static
    {
        $this->numeroDeTelephone = $numeroDeTelephone;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }
}
