<?php

namespace App\Form\Filter;

use Spiriit\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjetFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomprojet', Filters\TextFilterType::class, [
                'label' => 'Nom du Projet',
                'attr' => ['placeholder' => 'Selectionnez le nom du projet...'],
                'apply_filter' => function($filterQuery, $field, $values) {
                    if (!empty($values['value'])) {
                        $expr = $filterQuery->getExpr();
                        // Manually escape the LIKE parameter
                        $value = str_replace(
                            ['\\', '_', '%'],
                            ['\\\\', '\\_', '\\%'],
                            $values['value']
                        );
                        return $filterQuery->createCondition(
                            $expr->like($field, $expr->literal($value.'%'))
                        );
                    }
                    return null;
                }
            ])
            ->add('datecreation', Filters\DateFilterType::class, [
                'label' => 'Date de création',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker', 'placeholder' => 'Selectionnez la date...'],
                'apply_filter' => function($filterQuery, $field, $values) {
                    if (!empty($values['value'])) {
                        $expr = $filterQuery->getExpr();
                        return $filterQuery->createCondition(
                            $expr->eq($field, $expr->literal($values['value']->format('Y-m-d')))
                        );
                    }
                    return null;
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'validation_groups' => ['filtering']
        ]);
    }
}