<?php

// src/Form/PlanningType.php
namespace App\Form;

use App\Entity\Planning;
use App\Entity\Note;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanningType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('note', EntityType::class, [
                'class' => Note::class,
                'choices' => $options['user_notes'],
                'choice_label' => 'title',
                'attr' => ['class' => 'form-select']
            ])
            ->add('date_planifie', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control flatpickr-date']
            ])
            ->add('heure_debut', TimeType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'class' => 'form-control flatpickr-time',
                    'type' => 'time'
                ]
            ])
            ->add('heure_fin', TimeType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'class' => 'form-control flatpickr-time',
                    'type' => 'time'
                ]
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'Planifié' => 'planifié',
                    'En cours' => 'en cours',
                    'Terminé' => 'terminé'
                ],
                'attr' => ['class' => 'form-select']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Planning::class,
            'user_notes' => []
        ]);
    }
}
