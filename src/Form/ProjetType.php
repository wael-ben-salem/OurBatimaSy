<?php

namespace App\Form;

use App\Entity\Equipe;
use App\Entity\Projet;
use App\Entity\Terrain;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;

class ProjetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomprojet', null, [
                'label' => 'Nom du projet',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le nom du projet est obligatoire'
                    ]),
                    new Assert\Length([
                        'min' => 3,
                        'max' => 100,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères'
                    ])
                ],
                'attr' => [
                    'placeholder' => 'Ex: Villa moderne à Sousse'
                ]
            ])
            ->add('type', null, [
                'label' => 'Type de projet',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le type de projet est obligatoire'
                    ]),
                    new Assert\Length([
                        'max' => 50,
                        'maxMessage' => 'Le type ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('stylearch', null, [
                'label' => 'Style d\'architecture',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le style architectural est obligatoire'
                    ]),
                    new Assert\Length([
                        'max' => 100,
                        'maxMessage' => 'Le style ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('budget', null, [
                'label' => 'Budget (TND)',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le budget est obligatoire'
                    ]),
                    new Assert\Type([
                        'type' => 'numeric',
                        'message' => 'Le budget doit être un nombre'
                    ]),
                    new Assert\Positive([
                        'message' => 'Le budget doit être positif'
                    ])
                ]
            ])
            ->add('etat', ChoiceType::class, [
                'label' => 'État du projet',
                'choices' => [
                    'En cours' => 'En cours',
                    'En attente' => 'En attente',
                    'Terminé' => 'Terminé',
                    'Annulé' => 'Annulé'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'L\'état du projet est obligatoire'
                    ])
                ],
                'placeholder' => 'Choisir un état',
            ])
            ->add('idTerrain', EntityType::class, [
                'class' => Terrain::class,
                'choice_label' => 'emplacement',
                'label' => 'Emplacement du terrain',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le terrain est obligatoire'
                    ])
                ],
                'placeholder' => 'Sélectionner un terrain',
            ])
            ->add('idEquipe', EntityType::class, [
                'class' => Equipe::class,
                'choice_label' => 'nom',
                'required' => false,
                'placeholder' => 'Pas d\'équipe',
                'label' => 'Équipe responsable',
                // No constraints as per requirement
            ])
            ->add('nomClient', TextType::class, [
                'label' => 'Email du client',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'exemple@domaine.com'
                ],
                'constraints' => [
                    new Assert\Email([
                        'message' => 'Veuillez fournir une adresse email valide.',
                    ]),
                    new Assert\Length([
                        'max' => 180,
                        'maxMessage' => 'L\'email ne peut pas dépasser {{ limit }} caractères'
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Projet::class,
        ]);
    }
}