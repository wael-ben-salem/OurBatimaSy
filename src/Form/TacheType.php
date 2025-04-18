<?php

// src/Form/TacheType.php
namespace App\Form;

use App\Entity\Tache;
use App\Entity\Artisan;
use App\Entity\Projet;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\ORM\EntityRepository;

class TacheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description')
            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d'),
                ],
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'html5' => true,
            ])
            ->add('etat', ChoiceType::class, [
                'choices' => [
                    'En attente' => 'en_attente',
                    'En cours' => 'en_cours',
                    'Terminé' => 'termine',
                ],
                'required' => false,
            ])
            ->add('artisan', EntityType::class, [
                'class' => Artisan::class,
                'choice_label' => function (Artisan $artisan) {
                    return $artisan->getArtisan()->getPrenom() . ' ' . $artisan->getArtisan()->getNom();
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->innerJoin('a.artisan', 'u')
                        ->where('u.role = :role')
                        ->setParameter('role', 'artisan');
                },
            ])
            ->add('idProjet', EntityType::class, [
                'class' => Projet::class,
                'choice_label' => 'idProjet',
                'label' => 'Projet',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tache::class,
        ]);
    }
}
