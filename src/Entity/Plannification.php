<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Plannification
 */
#[ORM\Table(name: 'plannification')]
#[ORM\Index(name: 'id_tache', columns: ['id_tache'])]
#[ORM\Entity]
class Plannification
{
    /**
     * @var int
     */
    #[ORM\Column(name: 'id_plannification', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $idPlannification;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'priorite', type: 'string', length: 0, nullable: true)]
    private $priorite;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'date_planifiee', type: 'date', nullable: false)]
    #[Assert\NotBlank(message: "La date planifiée est obligatoire")]
    #[Assert\GreaterThanOrEqual(
        "today",
        message: "La date planifiée doit être aujourd'hui ou dans le futur"
    )]
    private $datePlanifiee;

    /**
     * @var \DateTime|null
     */
    #[ORM\Column(name: 'heure_debut', type: 'time', nullable: true)]
    #[Assert\Expression(
        "this.getHeureFin() == null or this.getHeureDebut() == null or this.getHeureDebut() < this.getHeureFin()",
        message: "L'heure de début doit être avant l'heure de fin"
    )]
    private $heureDebut;

    /**
     * @var \DateTime|null
     */
    #[ORM\Column(name: 'heure_fin', type: 'time', nullable: true)]
    private $heureFin;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'remarques', type: 'text', length: 65535, nullable: true)]
    #[Assert\Length(
        max: 65535,
        maxMessage: "Les remarques ne peuvent pas dépasser {{ limit }} caractères"
    )]
    private $remarques;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'statut', type: 'string', length: 0, nullable: true)]
    private $statut;

    /**
     * @var \Tache
     */
    #[ORM\JoinColumn(name: 'id_tache', referencedColumnName: 'id_tache')]
    #[ORM\ManyToOne(targetEntity: \Tache::class)]
    #[Assert\NotNull(message: "Une tâche doit être associée")]
    private $idTache;

    /**
     * @var Collection<int, SavedPlannification>
     */
    #[ORM\OneToMany(targetEntity: SavedPlannification::class, mappedBy: 'plannification')]
    private $savedPlannifications;

    public function __construct()
    {
        $this->savedPlannifications = new ArrayCollection();
    }

    public function getIdPlannification(): ?int
    {
        return $this->idPlannification;
    }

    public function getPriorite(): ?string
    {
        return $this->priorite;
    }

    public function setPriorite(?string $priorite): static
    {
        $this->priorite = $priorite;

        return $this;
    }

    public function getDatePlanifiee(): ?\DateTimeInterface
    {
        return $this->datePlanifiee;
    }

    public function setDatePlanifiee(\DateTimeInterface $datePlanifiee): static
    {
        $this->datePlanifiee = $datePlanifiee;

        return $this;
    }

    public function getHeureDebut(): ?\DateTimeInterface
    {
        return $this->heureDebut;
    }

    public function setHeureDebut(?\DateTimeInterface $heureDebut): static
    {
        $this->heureDebut = $heureDebut;

        return $this;
    }

    public function getHeureFin(): ?\DateTimeInterface
    {
        return $this->heureFin;
    }

    public function setHeureFin(?\DateTimeInterface $heureFin): static
    {
        $this->heureFin = $heureFin;

        return $this;
    }

    public function getRemarques(): ?string
    {
        return $this->remarques;
    }

    public function setRemarques(?string $remarques): static
    {
        $this->remarques = $remarques;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getIdTache(): ?Tache
    {
        return $this->idTache;
    }

    public function setIdTache(?Tache $idTache): static
    {
        $this->idTache = $idTache;

        return $this;
    }

    /**
     * @return Collection<int, SavedPlannification>
     */
    public function getSavedPlannifications(): Collection
    {
        return $this->savedPlannifications;
    }

    public function addSavedPlannification(SavedPlannification $savedPlannification): static
    {
        if (!$this->savedPlannifications->contains($savedPlannification)) {
            $this->savedPlannifications->add($savedPlannification);
            $savedPlannification->setPlannification($this);
        }

        return $this;
    }

    public function removeSavedPlannification(SavedPlannification $savedPlannification): static
    {
        if ($this->savedPlannifications->removeElement($savedPlannification)) {
            // set the owning side to null (unless already changed)
            if ($savedPlannification->getPlannification() === $this) {
                $savedPlannification->setPlannification(null);
            }
        }

        return $this;
    }


    public function __toString(): string
    {
        $task = $this->idTache ? (string)$this->idTache : 'No task';
        $date = $this->datePlanifiee ? $this->datePlanifiee->format('Y-m-d') : 'No date';
        $status = $this->statut ?? 'No status';
        return sprintf('Plan#%d: %s | %s | Status: %s',
            $this->idPlannification,
            $task,
            $date,
            $status
        );
    }
}

//to string

