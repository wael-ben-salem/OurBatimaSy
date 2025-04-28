<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SavedPlannification
 */
#[ORM\Table(name: 'saved_plannification')]
#[ORM\Entity]
class SavedPlannification
{
    /**
     * @var int
     */
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $id;

    /**
     * @var \Utilisateur|null
     */
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: 'App\Entity\Utilisateur')]
    private $user;

    /**
     * @var \Plannification
     */
    #[ORM\JoinColumn(name: 'plannification_id', referencedColumnName: 'id_plannification')]
    #[ORM\ManyToOne(targetEntity: 'App\Entity\Plannification')]
    #[Assert\NotNull(message: "A plannification must be associated")]
    private $plannification;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?Utilisateur
    {
        return $this->user;
    }

    public function setUser(?Utilisateur $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getPlannification(): ?Plannification
    {
        return $this->plannification;
    }

    public function setPlannification(?Plannification $plannification): static
    {
        $this->plannification = $plannification;

        return $this;
    }
}