<?php

namespace App\Form;

use App\Entity\Equipe;
use App\Entity\Projet;
use App\Entity\Terrain;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Email; // ✅ Required for the constraint

class ProjetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomprojet')
            ->add('type')
            ->add('stylearch')
            ->add('budget')
            ->add('etat', ChoiceType::class, [
                'choices' => [
                    'En cours' => 'En cours',
                    'En attente' => 'En attente',
                    'Fini' => 'Fini',
                    'Annulé' => 'Annulé'
                ],
                'placeholder' => 'Choisir un état',
            ])
            ->add('idTerrain', EntityType::class, [
                'class' => Terrain::class,
                'choice_label' => 'emplacement',
                'label' => 'Emplacement du terrain',
                'placeholder' => 'Sélectionner un terrain',
            ])
            ->add('idEquipe', EntityType::class, [
                'class' => Equipe::class,
                'choice_label' => 'nomEquipe',
                'required' => false,
                'placeholder' => 'Pas d\'équipe',
                'label' => 'Équipe responsable',
            ])
            ->add('nomClient', TextType::class, [
                'label' => 'Email du client',
                'mapped' => false,
                'required' => false, // ✅ make the field optional
                'constraints' => [
                    new Email([
                        'message' => 'Veuillez fournir une adresse email valide.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Projet::class,
        ]);
    }
}
