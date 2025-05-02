<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Email;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $artisanInfo = $options['artisan_info'] ?? null;
        $constructeurInfo = $options['constructeur_info'] ?? null;

        $builder
            ->add('email', TextType::class, [
                'label' => 'Email',
                'constraints' => [
                    new NotBlank(['message' => 'L\'email est obligatoire']),
                    new Email(['message' => 'Format d\'email invalide'])
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank(['message' => 'Le prénom est obligatoire']),
                    new Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÿ\s\-]{2,}$/u',
                        'message' => 'Caractères alphabétiques uniquement'
                    ])
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est obligatoire']),
                    new Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÿ\s\-]{2,}$/u',
                        'message' => 'Caractères alphabétiques uniquement'
                    ])
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[0-9]{5,15}$/',
                        'message' => 'Uniquement des chiffres (8 à 15 caractères)'
                    ])
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse',
                'required' => false,
                'constraints' => [
                    new Length(['min' => 10, 'minMessage' => 'Minimum 10 caractères'])
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'Client' => 'Client',
                    'Artisan' => 'Artisan',
                    'Constructeur' => 'Constructeur',
                    'GestionnaireStock' => 'GestionnaireStock',
                    'Admin' => 'Admin',
                ],
                'label' => 'Rôle',
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'utilisateur_role'
                ]
            ])
            // Champs Artisan
            ->add('specialiteArtisan', ChoiceType::class, [
                'choices' => [
                    'Menuiserie' => 'Menuiserie',
                    'Maçonnerie' => 'Maçonnerie',
                    'Électricité' => 'Électricité',
                    'Plomberie' => 'Plomberie',
                    'Autre' => 'Autre',
                ],
                'required' => false,
                'mapped' => false,
                'label' => 'Spécialité Artisan',
                'data' => $artisanInfo ? $artisanInfo->getSpecialite() : null,
                'attr' => ['class' => 'form-control']
            ])
            ->add('salaireHeureArtisan', NumberType::class, [
                'required' => false,
                'mapped' => false,
                'label' => 'Salaire / heure Artisan (DT)',
                'data' => $artisanInfo ? $artisanInfo->getSalaireHeure() : null,
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ]
            ])
            // Champs Constructeur
            ->add('specialiteConstructeur', TextType::class, [
                'required' => false,
                'mapped' => false,
                'label' => 'Spécialité Constructeur',
                'data' => $constructeurInfo ? $constructeurInfo->getSpecialite() : null,
                'attr' => ['class' => 'form-control']
            ])
            ->add('salaireHeureConstructeur', NumberType::class, [
                'required' => false,
                'mapped' => false,
                'label' => 'Salaire / heure Constructeur (DT)',
                'data' => $constructeurInfo ? $constructeurInfo->getSalaireHeure() : null,
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ]
            ]);
    }

    private function addRoleSpecificFields($form, $role, $artisanInfo = null, $constructeurInfo = null): void
    {
        $form->remove('specialiteArtisan');
        $form->remove('salaireHeureArtisan');
        $form->remove('specialiteConstructeur');
        $form->remove('salaireHeureConstructeur');

        if ($role === 'Artisan') {
            $form->add('specialiteArtisan', ChoiceType::class, [
                'choices' => [
                    'Menuiserie' => 'Menuiserie',
                    'Maçonnerie' => 'Maçonnerie',
                    'Électricité' => 'Électricité',
                    'Plomberie' => 'Plomberie',
                    'Autre' => 'Autre',
                ],
                'required' => true,
                'mapped' => false,
                'label' => 'Spécialité Artisan',
                'constraints' => [new NotBlank()],
                'data' => $artisanInfo?->getSpecialite(),
                'attr' => ['class' => 'form-control']
            ])
            ->add('salaireHeureArtisan', NumberType::class, [
                'required' => true,
                'mapped' => false,
                'label' => 'Salaire / heure (DT)',
                'constraints' => [
                    new NotBlank(),
                    new Regex(['pattern' => '/^\d+(\.\d{1,2})?$/'])
                ],
                'data' => $artisanInfo?->getSalaireHeure(),
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ]
            ]);
        }

        if ($role === 'Constructeur') {
            $form->add('specialiteConstructeur', TextType::class, [
                'required' => true,
                'mapped' => false,
                'label' => 'Spécialité Constructeur',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 3])
                ],
                'data' => $constructeurInfo?->getSpecialite(),
                'attr' => ['class' => 'form-control']
            ])
            ->add('salaireHeureConstructeur', NumberType::class, [
                'required' => true,
                'mapped' => false,
                'label' => 'Salaire / heure (DT)',
                'constraints' => [
                    new NotBlank(),
                    new Regex(['pattern' => '/^\d+(\.\d{1,2})?$/'])
                ],
                'data' => $constructeurInfo?->getSalaireHeure(),
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ]
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
            'artisan_info' => null,
            'constructeur_info' => null,
        ]);

        $resolver->setAllowedTypes('artisan_info', ['null', 'App\Entity\Artisan']);
        $resolver->setAllowedTypes('constructeur_info', ['null', 'App\Entity\Constructeur']);
    }
}