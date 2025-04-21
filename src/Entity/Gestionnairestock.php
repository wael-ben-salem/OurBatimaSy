<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Gestionnairestock
 */
#[ORM\Table(name: 'gestionnairestock')]
#[ORM\Entity]
class Gestionnairestock
{
    /**
     * @var \Utilisateur
     */
    #[ORM\JoinColumn(name: 'gestionnairestock_id', referencedColumnName: 'id')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\OneToOne(targetEntity: \Utilisateur::class)]
    private $gestionnairestock;

    public function getGestionnairestock(): ?Utilisateur
    {
        return $this->gestionnairestock;
    }

    public function setGestionnairestock(?Utilisateur $gestionnairestock): static
    {
        $this->gestionnairestock = $gestionnairestock;

        return $this;
    }


}