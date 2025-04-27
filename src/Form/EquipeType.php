<?php
// src/Form/EquipeType.php

namespace App\Form;

use App\Entity\Equipe;
use App\Entity\Constructeur;
use App\Entity\Gestionnairestock;
use App\Entity\Artisan;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EquipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', TextType::class, [
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Team name'
            ]
        ])            ->add('constructeur', EntityType::class, [
                'class' => Constructeur::class,
                'choice_label' => function(Constructeur $constructeur) {
                    $user = $constructeur->getConstructeur(); // Accès au User via Constructeur
                    return $user->getNom() . ' ' . $user->getPrenom();
                },
            
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('c')
                        ->join('c.constructeur', 'u')
                        ->where('u.role = :role')
                        ->setParameter('role', 'Constructeur');
                }
            ])
            ->add('gestionnairestock', EntityType::class, [
                'class' => Gestionnairestock::class,
                'choice_label' => function(Gestionnairestock $gestionnaire) {
                    $user = $gestionnaire->getGestionnairestock(); // Accès au User via GestionnaireStock
                    return $user->getNom() . ' ' . $user->getPrenom();
                },
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('g')
                        ->join('g.gestionnairestock', 'u')
                        ->where('u.role = :role')
                        ->setParameter('role', 'GestionnaireStock');
                }
            ])
            ->add('artisan', EntityType::class, [
                'class' => Artisan::class,
                'choice_label' => function(Artisan $artisan) {
                    $user = $artisan->getArtisan();
                    return sprintf('%s %s (%s)', 
                        $user->getNom(), 
                        $user->getPrenom(), 
                        $artisan->getSpecialite() // Accès à la spécialité via Artisan
                    );
                },
                'multiple' => true,
                'expanded' => false,
                'attr' => ['class' => 'select2'],
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('a')
                        ->join('a.artisan', 'u')
                        ->where('u.role = :role')
                        ->setParameter('role', 'Artisan');
                }
            ])
            ->add('rating', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                    'max' => 5,
                    'step' => 0.1,
                    'placeholder' => 'Rating (0-5)'
                ],
                'required' => false
            ]);
            }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipe::class,
        ]);
    }
}