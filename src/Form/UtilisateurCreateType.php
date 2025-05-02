<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;

class UtilisateurCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email *',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre email']),
                    new Email(['message' => 'Adresse email invalide'])
                ],
                'attr' => ['class' => 'form-control']
            ]) ->add('email', EmailType::class, [
                'label' => 'Email *',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre email']),
                    new Email(['message' => 'Adresse email invalide'])
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom *',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre prénom']),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le prénom doit contenir au moins {{ limit }} caractères'
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÿ\s\-]+$/u',
                        'message' => 'Le prénom ne doit contenir que des lettres'
                    ])
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom *',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre nom']),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères'
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÿ\s\-]+$/u',
                        'message' => 'Le nom ne doit contenir que des lettres'
                    ])
                ],
                'attr' => ['class' => 'form-control']
            ])
            
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone *',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre numéro']),
                    new Regex([
                        'pattern' => '/^[0-9]{8,20}$/',
                        'message' => 'Uniquement des chiffres (ex: 22123456)'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'telephone'
                ]
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse *',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner une adresse']),
                    new Length(['min' => 10, 'minMessage' => 'Adresse trop courte'])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'readonly' => 'readonly',
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#mapModal'
                ]
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe *',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un mot de passe']),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Minimum {{ limit }} caractères',
                        'max' => 4096
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/',
                        'message' => 'Doit contenir 1 majuscule, 1 minuscule et 1 chiffre'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'autocomplete' => 'new-password'
                ]
            ])
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'Client' => 'Client',
                    'Artisan' => 'Artisan',
                    'Constructeur' => 'Constructeur',
                    'GestionnaireStock' => 'GestionnaireStock',
                    'Admin' => 'Admin',
                ],
                'label' => 'Rôle *',
                'attr' => ['class' => 'form-control']
            ])
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
                'attr' => ['class' => 'form-control']
            ])
            ->add('salaireHeureArtisan', NumberType::class, [
                'required' => false,
                'mapped' => false,
                'label' => 'Salaire / heure (DT)',
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ]
            ])
            ->add('specialiteConstructeur', TextType::class, [
                'required' => false,
                'mapped' => false,
                'label' => 'Spécialité Constructeur',
                'attr' => ['class' => 'form-control']
            ])
            ->add('salaireHeureConstructeur', NumberType::class, [
                'required' => false,
                'mapped' => false,
                'label' => 'Salaire / heure (DT)',
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
            'attr' => ['id' => 'user_form']
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'utilisateur';
    }
}