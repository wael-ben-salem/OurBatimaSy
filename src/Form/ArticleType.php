<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Etapeprojet;
use App\Entity\Fournisseur;
use App\Entity\Stock;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('description')
            ->add('prixUnitaire')
            
            ->add('etapeprojet', EntityType::class, [
                'class' => Etapeprojet::class,
                'choice_label' => 'nometape', // Display the name
                'placeholder' => 'Sélectionnez une étape de projet',
                'required' => false,
            ])
            ->add('stock', EntityType::class, [
                'class' => Stock::class,
                'choice_label' => 'nom', // Display the name
                'placeholder' => 'Sélectionnez un stock',
                'required' => false,
            ])
            ->add('fournisseur', EntityType::class, [
                'class' => Fournisseur::class,
                'choice_label' => 'nom', // Display the name
                'placeholder' => 'Sélectionnez un fournisseur',
                'required' => false,
            ])
            ->add('photoFile', FileType::class, [
                'label' => 'Photo (JPG, PNG)',
                'mapped' => false, // This field is not mapped to the Article entity
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file (JPG or PNG).',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
