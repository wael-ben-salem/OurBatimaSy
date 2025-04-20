<?php

namespace App\Form;

use App\Entity\Constructeur;
use App\Entity\Gestionnairestock;
use App\Entity\Artisan;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class EquipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('constructeur', EntityType::class, [
                'class' => Constructeur::class,
                'choice_label' => 'nom',
                'attr' => ['class' => 'form-select']
            ])
            ->add('gestionnairestock', EntityType::class, [
                'class' => Gestionnairestock::class,
                'choice_label' => 'nom',
                'attr' => ['class' => 'form-select']
            ])
            ->add('artisan', EntityType::class, [
                'class' => Artisan::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'attr' => ['class' => 'form-select']
            ]);
    }
}
