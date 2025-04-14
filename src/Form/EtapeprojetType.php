<?php

namespace App\Form;

use App\Entity\Etapeprojet;
use App\Entity\Projet;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EtapeprojetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nometape', null, [
                'label' => 'Nom de l\'étape'
            ])
            ->add('description', null, [
                'required' => false
            ])
            ->add('datedebut', null, [
                'label' => 'Date de début',
                'widget' => 'single_text',
            ])
            ->add('datefin', null, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
            ])
            ->add('statut', ChoiceType::class, [
                'label' => 'État',
                'choices' => [
                    'En cours' => 'En cours',
                    'En attente' => 'En attente',
                    'Terminé' => 'Terminé',
                    'Annulé' => 'Annulé'
                ],
                'placeholder' => 'Sélectionnez un statut',
            ])
            ->add('montant')
            ->add('idProjet', EntityType::class, [
                'label' => 'Projet associé',
                'class' => Projet::class,
                'choice_label' => 'nomProjet',
                'placeholder' => 'Sélectionnez un projet',
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