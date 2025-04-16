<?php

namespace App\Form;

use App\Entity\Terrain;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class TerrainType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('emplacement', null, [
                'label' => 'Emplacement du terrain',
                'attr' => [
                    'placeholder' => 'Adresse ou localisation du terrain'
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
            ->add('caracteristiques', null, [
                'label' => 'Caractéristiques principales',
                'attr' => [
                    'placeholder' => 'Décrivez les caractéristiques du terrain'
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
            ->add('superficie', null, [
                'label' => 'Superficie (m²)',
                'attr' => [
                    'placeholder' => 'Surface en mètres carrés'
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
            ->add('detailsgeo', null, [
                'label' => 'Détails géographiques',
                'attr' => [
                    'placeholder' => 'Pente, orientation, particularités...'
                ],
                'required' => false, // Make this field optional
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Terrain::class,
        ]);
    }
}