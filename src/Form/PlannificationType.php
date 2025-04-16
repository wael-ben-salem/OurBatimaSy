<?php

// src/Form/PlannificationType.php
namespace App\Form;

use App\Entity\Plannification;
use App\Entity\Tache;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PlannificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('priorite', ChoiceType::class, [
                'choices' => [
                    'Haute' => 'Haute',
                    'Moyenne' => 'Moyenne',
                    'Basse' => 'Basse',
                ],
                'required' => false,
            ])
            ->add('datePlanifiee', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('heureDebut', TimeType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('heureFin', TimeType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('remarques', TextareaType::class, [
                'required' => false,
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'Planifié' => 'Planifié',
                    'En cours' => 'En cours',
                    'Terminé' => 'Terminé',
                    'Annulé' => 'Annulé',
                ],
                'required' => false,
            ])
            ->add('idTache', EntityType::class, [
                'class' => Tache::class,
                'choice_label' => 'description',
                'label' => 'Tâche associée'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Plannification::class,
        ]);
    }
}