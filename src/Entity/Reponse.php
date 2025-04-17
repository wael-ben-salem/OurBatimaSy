<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    /**
     * @var Collection<int, Reclamation>
     */
    #[ORM\ManyToMany(targetEntity: Reclamation::class)]
    private Collection $id_Reclamation;

    public function __construct()
    {
        $this->id_Reclamation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection<int, Reclamation>
     */
    public function getIdReclamation(): Collection
    {
        return $this->id_Reclamation;
    }

    public function addIdReclamation(Reclamation $idReclamation): static
    {
        if (!$this->id_Reclamation->contains($idReclamation)) {
            $this->id_Reclamation->add($idReclamation);
        }

        return $this;
    }

    public function removeIdReclamation(Reclamation $idReclamation): static
    {
        $this->id_Reclamation->removeElement($idReclamation);

        return $this;
    }
}
