<?php

namespace App\Form;

use App\Entity\Contrat;
use App\Entity\Projet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType; // Add this line


class ContratType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('typeContrat', ChoiceType::class, [
                'label' => 'Type de contrat',

                'choices' => [
                    'Contrat Client' => 'client',
                    'Contrat Constructeur' => 'constructeur',
                   

                ],
              
              
                'attr' => ['class' => 'form-control',
                'placeholder' => 'selectioner type contrat '

                ]
                ])

               


            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text' ,
                'required' => true,

                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La date de début est obligatoire'
                    ])
                ],
                'attr' => ['class' => 'form-control datepicker']
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'required' => true,

                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La date de fin est obligatoire'
                    ])
                ],
                'attr' => ['class' => 'form-control datepicker']
            ])
          
            ->add('montantTotal', NumberType::class, [
                'required' => true,

                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le montant est obligatoire'
                    ]),
                    new Assert\Positive([
                        'message' => 'Le montant doit être positif'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'En TND'
                ]
            ])
            ->add('idProjet', EntityType::class, [
                'class' => Projet::class,
                'choice_label' => 'nomprojet',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le projet est obligatoire'
                    ])
                ],
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contrat::class,
            'constraints' => [
                new Assert\Callback([$this, 'validateDates'])
            ]
        ]);
    }

    public function validateDates(Contrat $contrat, ExecutionContextInterface $context)
    {
        if ($contrat->getDateDebut() && $contrat->getDateFin()) {
            if ($contrat->getDateDebut() > $contrat->getDateFin()) {
                $context->buildViolation('La date de fin doit être postérieure à la date de début')
                    ->atPath('dateFin')
                    ->addViolation();
            }
        }
    }
}