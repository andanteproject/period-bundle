<?php

declare(strict_types=1);


namespace Andante\PeriodBundle\Form;

use League\Period\Period;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BoundaryTypeChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => [
                'Include start and exclude end' => Period::INCLUDE_START_EXCLUDE_END,
                'Include both start and end' => Period::INCLUDE_ALL,
                'Exclude start and include end' => Period::EXCLUDE_START_INCLUDE_END,
                'Exclude both start and end' => Period::EXCLUDE_ALL,
            ],
            'empty_data' => Period::INCLUDE_START_EXCLUDE_END,
            'multiple' => false,
            'expanded' => false,
            'choice_translation_domain' => 'AndantePeriodBundle'
        ]);
    }


    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
