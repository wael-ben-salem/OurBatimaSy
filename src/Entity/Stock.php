<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Stock
 */
#[ORM\Table(name: 'stock')]
#[ORM\Entity]
class Stock
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
    #[Assert\NotBlank(message: 'The name cannot be blank.')]
    #[Assert\Length(
        min: 3,
        minMessage: 'The name must be at least {{ limit }} characters long.'
    )]
    private $nom;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'emplacement', type: 'string', length: 255, nullable: true)]
    #[Assert\Length(
        min: 3,
        minMessage: 'The emplacement must be at least {{ limit }} characters long.'
    )]
    private $emplacement;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'dateCreation', type: 'datetime', nullable: false)]
    #[Assert\NotNull(message: 'The creation date is mandatory.')]
    private $datecreation;

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

    public function getEmplacement(): ?string
    {
        return $this->emplacement;
    }

    public function setEmplacement(?string $emplacement): static
    {
        $this->emplacement = $emplacement;

        return $this;
    }

    public function getDatecreation(): ?\DateTime
    {
        return $this->datecreation;
    }

    public function setDatecreation(\DateTime $datecreation): static
    {
        $this->datecreation = $datecreation;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'nom' => $this->getNom(),
            'emplacement' => $this->getEmplacement(),
            'datecreation' => $this->getDatecreation()->format('Y-m-d H:i:s'),
        ];
    }
}
