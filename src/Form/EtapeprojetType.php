<?php

namespace App\Form;

use App\Entity\Etapeprojet;
use App\Entity\Projet;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class EtapeprojetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nometape', null, [
                'label' => 'Nom de l\'étape',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le nom de l\'étape est obligatoire'
                    ]),
                    new Assert\Length([
                        'min' => 3,
                        'max' => 100,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères'
                    ])
                ],
                'attr' => [
                    'placeholder' => 'Ex: Fondations, Gros œuvre, Électricité...'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description détaillée',
                'required' => true, 
                'constraints' => [
                    new Assert\NotBlank([ 
                        'message' => 'La description est obligatoire'
                    ]),
                    new Assert\Length([
                        'min' => 5, 
                        'max' => 500,
                        'minMessage' => 'La description doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères'
                    ])
                ],
                'attr' => [
                    'placeholder' => 'Détails sur cette étape du projet',
                    'rows' => 5
                ]
            ])
            ->add('datedebut', null, [
                'label' => 'Date de début prévue',
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La date de début est obligatoire'
                    ]),
                    new Assert\Type([
                        'type' => \DateTimeInterface::class,
                        'message' => 'Veuillez entrer une date valide'
                    ])
                ],
                'attr' => [
                    'class' => 'datepicker'
                ]
            ])
            ->add('datefin', null, [
                'label' => 'Date de fin prévue',
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La date de fin est obligatoire'
                    ]),
                    new Assert\Type([
                        'type' => \DateTimeInterface::class,
                        'message' => 'Veuillez entrer une date valide'
                    ]),
                    new Assert\GreaterThan([
                        'propertyPath' => 'parent.all[datedebut].data',
                        'message' => 'La date de fin doit être postérieure à la date de début'
                    ])
                ],
                'attr' => [
                    'class' => 'datepicker'
                ]
            ])
            ->add('statut', ChoiceType::class, [
                'label' => 'État d\'avancement',
                'choices' => [
                    'En cours' => 'En cours',
                    'En attente' => 'En attente',
                    'Terminé' => 'Terminé',
                    'Annulé' => 'Annulé'
                    
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le statut est obligatoire'
                    ])
                ],
                'placeholder' => 'Sélectionnez un statut',
                'attr' => [
                    'class' => 'select2'
                ]
            ])
            ->add('montant', null, [
                'label' => 'Budget alloué (TND)',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le montant est obligatoire'
                    ]),
                    new Assert\Type([
                        'type' => 'numeric',
                        'message' => 'Le montant doit être un nombre'
                    ]),
                    new Assert\PositiveOrZero([
                        'message' => 'Le montant ne peut pas être négatif'
                    ])
                ],
                'attr' => [
                    'placeholder' => 'Montant en dinars tunisiens'
                ]
            ])
            ->add('idProjet', EntityType::class, [
                'label' => 'Projet associé',
                'class' => Projet::class,
                'choice_label' => 'nomProjet',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le projet associé est obligatoire'
                    ])
                ],
                'placeholder' => 'Sélectionnez un projet',
                'attr' => [
                    'class' => 'select2'
                ]
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