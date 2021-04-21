<?php

namespace Andante\PeriodBundle\Tests\Form;

use Andante\PeriodBundle\Form\PeriodType;
use League\Period\Period;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Forms;

class PeriodTypeTest extends TestCase
{
    public function testCreatePeriod(): void
    {
        $factory = Forms::createFormFactoryBuilder()->getFormFactory();

        $builder = $factory->createBuilder(PeriodType::class, null, [
            'start_date_options' => [
                'widget' => 'single_text',
            ],
            'end_date_options' => [
                'widget' => 'single_text',
            ],
        ]);
        $form = $builder->getForm();

        $form->submit([
            'start' => '2020-01-01T00:00:00',
            'end' => '2020-01-02T00:00:00',
        ]);

        $data = $form->getData();

        self::assertEquals(
            $data,
            Period::fromDatepoint(
                \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-01 00:00:00'),
                \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-02 00:00:00'),
                Period::INCLUDE_START_EXCLUDE_END
            )
        );
    }

    public function testCreateNull(): void
    {
        $factory = Forms::createFormFactoryBuilder()->getFormFactory();

        $builder = $factory->createBuilder(PeriodType::class, null, [
            'start_date_options' => [
                'widget' => 'single_text',
            ],
            'end_date_options' => [
                'widget' => 'single_text',
            ],
        ]);
        $form = $builder->getForm();

        $form->submit([]);

        $data = $form->getData();

        self::assertNull($data);
    }

    public function testPreSetData(): void
    {
        $factory = Forms::createFormFactoryBuilder()->getFormFactory();

        $startDate = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-01 00:00:00');
        $endDate = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-02 00:00:00');
        $boundaryType = Period::INCLUDE_START_EXCLUDE_END;

        $period = Period::fromDatepoint(
            $startDate,
            $endDate,
            $boundaryType
        );
        $builder = $factory->createBuilder(
            PeriodType::class,
            $period
        );

        $form = $builder->getForm();

        self::assertEquals($startDate, $form->get('start')->getData());
        self::assertEquals($endDate, $form->get('end')->getData());
        self::assertSame($period, $form->getData());
    }

    public function testSetToNull(): void
    {
        $factory = Forms::createFormFactoryBuilder()->getFormFactory();

        $startDate = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-01 00:00:00');
        $endDate = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-02 00:00:00');
        $boundaryType = Period::INCLUDE_START_EXCLUDE_END;

        $period = Period::fromDatepoint(
            $startDate,
            $endDate,
            $boundaryType
        );
        $builder = $factory->createBuilder(
            PeriodType::class,
            $period
        );

        $form = $builder->getForm();

        $form->submit([]);

        self::assertNull($form->get('start')->getData());
        self::assertNull($form->get('end')->getData());
        self::assertNull($form->getData());
    }
}
