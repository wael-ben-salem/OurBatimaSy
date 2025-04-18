<?php

namespace App\Form;

use App\Entity\Artisan;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ArtisanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('specialite', ChoiceType::class, [
                'choices' => [
                    'Menuiserie' => 'Menuiserie',
                    'Maçonnerie' => 'Maçonnerie',
                    'Électricité' => 'Électricité',
                    'Plomberie' => 'Plomberie',
                    'Autre' => 'Autre'
                ],
                'label' => 'Spécialité'
            ])
            ->add('salaireHeure', NumberType::class, [
                'label' => 'Salaire horaire (€)',
                'scale' => 2
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Artisan::class,
        ]);
    }
}