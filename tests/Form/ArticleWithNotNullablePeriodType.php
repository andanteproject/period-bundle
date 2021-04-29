<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Tests\Form;

use Andante\PeriodBundle\Form\PeriodType;
use Andante\PeriodBundle\Tests\Fixtures\Entity\ArticleWithNotNullablePeriod;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ArticleWithNotNullablePeriodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('period', PeriodType::class, [
                'start_date_options' => [
                    'widget' => 'single_text',
                ],
                'end_date_options' => [
                    'widget' => 'single_text',
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ArticleWithNotNullablePeriod::class,
        ]);
    }
}
