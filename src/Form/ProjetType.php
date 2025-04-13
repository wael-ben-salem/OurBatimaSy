<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Equipe;
use App\Entity\Projet;
use App\Entity\Terrain;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;



class ProjetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type')
            ->add('stylearch')
            ->add('budget')
            ->add('etat', ChoiceType::class, [
                'choices' => [
                    'En attente' => 'en attente',
                    'En cours' => 'en cours',
                    'Fini' => 'fini',
                ],
                'placeholder' => 'Choisir un état',
            ])
            ->add('datecreation', null, [
                'widget' => 'single_text',
            ])
            ->add('nomprojet')
            ->add('idTerrain', EntityType::class, [
                'class' => Terrain::class,
                'choice_label' => 'emplacement',
                'label' => 'Emplacement du terrain',
                'placeholder' => 'Sélectionner un terrain',
            ])
            ->add('idEquipe', EntityType::class, [
                'class' => Equipe::class,
                'choice_label' => 'nomEquipe',
            ])
            ->add('idClient', EntityType::class, [
                'class' => Client::class,
                'choice_label' => 'nomClient',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Projet::class,
        ]);
    }
}
