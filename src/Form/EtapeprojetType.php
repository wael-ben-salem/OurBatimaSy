<?php

namespace App\Form;

use App\Entity\Etapeprojet;
use App\Entity\Projet;
use App\Entity\Rapport;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EtapeprojetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nometape')
            ->add('description')
            ->add('datedebut', null, [
                'widget' => 'single_text',
            ])
            ->add('datefin', null, [
                'widget' => 'single_text',
            ])
            ->add('statut')
            ->add('montant')
            ->add('idProjet', EntityType::class, [
                'class' => Projet::class,
                'choice_label' => 'nomProjet',
            ])
            ->add('idRapport', EntityType::class, [
                'class' => Rapport::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Etapeprojet::class,
        ]);
    }
}
