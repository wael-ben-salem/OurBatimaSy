<?php

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
                'placeholder' => 'Choisir une priorité',
            ])
            ->add('datePlanifiee', DateType::class, [
                'widget' => 'single_text',
                'data' => new \DateTime(), // Set default to today
                'attr' => ['min' => (new \DateTime())->format('Y-m-d')], // Set min date to today
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
                'attr' => ['rows' => 5],
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'Planifié' => 'Planifié',
                    'En cours' => 'En cours',
                    'Terminé' => 'Terminé',
                    'Annulé' => 'Annulé',
                ],
                'required' => false,
                'placeholder' => 'Choisir un statut',
            ])
            ->add('idTache', EntityType::class, [
                'class' => Tache::class,
                'choice_label' => 'description',
                'label' => 'Tâche associée',
                'placeholder' => 'Sélectionner une tâche',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Plannification::class,
        ]);
    }
}