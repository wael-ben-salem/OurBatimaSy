<?php

namespace App\Form;

use App\Entity\Reclamation;
use App\Entity\Reponse;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;

class Reponse1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a description']),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Your description should be at least {{ limit }} characters',
                        'max' => 1000,
                        'maxMessage' => 'Your description cannot be longer than {{ limit }} characters'
                    ])
                ],
                'attr' => ['rows' => 5]
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'Pending' => 'Pending',
                    'In Progress' => 'In Progress',
                    'Resolved' => 'Resolved',
                    'Closed' => 'Closed'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Please select a status'])
                ]
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new NotNull(['message' => 'Please select a date'])
                ]
            ])
            ->add('id_Reclamation', EntityType::class, [
                'class' => Reclamation::class,
                'choice_label' => function(Reclamation $reclamation) {
                    return sprintf('#%d - %s', $reclamation->getId(), substr($reclamation->getDescription(), 0, 30));
                },
                'label' => 'Réclamation associée',
                'placeholder' => 'Choisir une réclamation',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner une réclamation'])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reponse::class,
        ]);
    }
}
