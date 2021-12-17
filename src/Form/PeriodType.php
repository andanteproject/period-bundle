<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Form;

use Andante\PeriodBundle\Form\DataMapper\PeriodDataMapper;
use League\Period\Exception;
use League\Period\Period;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PeriodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add($options['start_date_child_name'], $options['start_date_form_type'], \array_merge_recursive(
            [
                'label' => 'Start',
                'input' => 'datetime_immutable',
                'property_path' => 'startDate',
            ],
            $options['start_date_options']
        ));
        $builder->add($options['end_date_child_name'], $options['end_date_form_type'], \array_merge_recursive(
            [
                'label' => 'End',
                'input' => 'datetime_immutable',
                'property_path' => 'endDate',
            ],
            $options['end_date_options']
        ));
        if ($options['boundary_type_choice']) {
            $builder->add($options['boundary_type_child_name'], BoundaryTypeChoiceType::class, \array_merge_recursive(
                [
                    'label' => 'Boundary type',
                    'property_path' => 'boundaryType',
                ],
                $options['boundary_type_options']
            ));
        }
        $builder->setDataMapper(
            new PeriodDataMapper(
                $options['default_boundary_type'],
                $options['start_date_child_name'],
                $options['end_date_child_name'],
                $options['boundary_type_child_name'],
                $options['allow_null']
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Period::class,
            'empty_data' => function (FormInterface $form) {
                $config = $form->getConfig();
                $startDateChildName = $config->getOption('start_date_child_name');
                $endDateChildName = $config->getOption('end_date_child_name');
                $boundaryTypeChildName = $config->getOption('boundary_type_child_name');

                $startDate = $form->get($startDateChildName)->getData();
                $endDate = $form->get($endDateChildName)->getData();
                $boundaryType = $form->has($boundaryTypeChildName) ?
                    $form->get($boundaryTypeChildName)->getData() : $config->getOption('default_boundary_type');

                if ($startDate instanceof \DateTimeInterface && $endDate instanceof \DateTimeInterface) {
                    try {
                        return Period::fromDatepoint($startDate, $endDate, $boundaryType);
                    } catch (Exception $e) {
                    }
                }

                return null;
            },
            'translation_domain' => 'AndantePeriodBundle',
            'default_boundary_type' => Period::INCLUDE_START_EXCLUDE_END,
            'boundary_type_choice' => false,
            'start_date_child_name' => 'start',
            'start_date_form_type' => DateTimeType::class,
            'end_date_child_name' => 'end',
            'end_date_form_type' => DateTimeType::class,
            'start_date_options' => [],
            'end_date_options' => [],
            'boundary_type_child_name' => 'boundary',
            'boundary_type_options' => [],
            'allow_null' => true,
            'error_bubbling' => false,
        ]);

        $resolver->setAllowedValues('default_boundary_type', [
            Period::INCLUDE_START_EXCLUDE_END,
            Period::INCLUDE_ALL,
            Period::EXCLUDE_START_INCLUDE_END,
            Period::EXCLUDE_ALL,
        ]);
        $resolver->setAllowedTypes('boundary_type_choice', 'bool');
        $resolver->setAllowedTypes('start_date_options', 'array');
        $resolver->setAllowedTypes('end_date_options', 'array');
        $resolver->setAllowedTypes('boundary_type_options', 'array');

        $resolver->setAllowedTypes('start_date_child_name', 'string');
        $resolver->setAllowedTypes('end_date_child_name', 'string');
        $resolver->setAllowedTypes('boundary_type_child_name', 'string');
        $resolver->setAllowedTypes('allow_null', 'bool');
    }
}
