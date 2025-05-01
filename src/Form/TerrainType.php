<?php

namespace App\Form;

use App\Entity\Terrain;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class TerrainType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('emplacement', TextType::class, [
                'label' => 'Emplacement du terrain',
                'attr' => [
                    'placeholder' => 'Adresse ou localisation du terrain',
                    'class' => 'form-control location-field',
                    'readonly' => false // Ensure field is editable
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'L\'emplacement du terrain est obligatoire',
                    ]),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'L\'emplacement ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('caracteristiques', TextareaType::class, [
                'label' => 'Caractéristiques principales',
                'attr' => [
                    'placeholder' => 'Décrivez les caractéristiques du terrain',
                    'class' => 'form-control',
                    'rows' => 3
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Les caractéristiques du terrain sont obligatoires',
                    ]),
                    new Assert\Length([
                        'max' => 500,
                        'maxMessage' => 'Les caractéristiques ne peuvent pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('superficie', NumberType::class, [
                'label' => 'Superficie (m²)',
                'attr' => [
                    'placeholder' => 'Surface en mètres carrés',
                    'class' => 'form-control',
                    'min' => 0,
                    'step' => 'any'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La superficie est obligatoire',
                    ]),
                    new Assert\Type([
                        'type' => 'numeric',
                        'message' => 'La superficie doit être un nombre',
                    ]),
                    new Assert\Positive([
                        'message' => 'La superficie doit être positive',
                    ]),
                ],
            ])
            ->add('detailsgeo', TextareaType::class, [
                'label' => 'Détails géographiques',
                'attr' => [
                    'placeholder' => 'Coordonnées GPS, pente, orientation...',
                    'class' => 'form-control geo-details-field',
                    'rows' => 2,
                    'readonly' => false // Ensure field is editable
                ],
                'required' => false,
            ])
            ->add('latitude', HiddenType::class, [
                'attr' => [
                    'class' => 'latitude-field'
                ]
            ])
            ->add('longitude', HiddenType::class, [
                'attr' => [
                    'class' => 'longitude-field'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Terrain::class,
            'validation_groups' => ['Default'],
            'attr' => [
                'novalidate' => 'novalidate',
                'class' => 'needs-validation g-3'
            ]
        ]);
    }
}