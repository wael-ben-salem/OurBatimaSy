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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom *',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre nom']),
                    new Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÿ\s\-]{2,}$/u',
                        'message' => 'Le nom doit contenir au moins 2 caractères alphabétiques'
                    ])
                ],
                'attr' => ['placeholder' => 'Ex: Dupont']
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom *',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre prénom']),
                    new Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÿ\s\-]{2,}$/u',
                        'message' => 'Le prénom doit contenir au moins 2 caractères alphabétiques'
                    ])
                ],
                'attr' => ['placeholder' => 'Ex: Jean']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email *',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre email']),
                    new Email(['message' => 'Adresse email invalide'])
                ],
                'attr' => ['placeholder' => 'exemple@domaine.com']
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone *',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre numéro']),
                    new Regex([
                        'pattern' => '/^\+?[0-9\s]{8,20}$/',
                        'message' => 'Format international requis (ex: +216 22 123 456)'
                    ])
                ],
                'attr' => ['id' => 'telephone']
            ])
            ->add('adresse', TextareaType::class, [
                'label' => 'Adresse *',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner une adresse']),
                    new Length(['min' => 10, 'minMessage' => 'Adresse trop courte'])
                ],
                'attr' => ['readonly' => true]
            ])
            ->add('plainPassword', PasswordType::class, [
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
                'attr' => ['autocomplete' => 'new-password']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
            'attr' => ['novalidate' => 'novalidate']
        ]);
    }
}