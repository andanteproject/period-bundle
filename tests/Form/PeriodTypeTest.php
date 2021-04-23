<?php

namespace Andante\PeriodBundle\Tests\Form;

use Andante\PeriodBundle\Form\PeriodType;
use League\Period\Period;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Validator\Validation;

class PeriodTypeTest extends TestCase
{
    protected function getFormFactory(): FormFactoryInterface
    {
        return Forms::createFormFactoryBuilder()
            ->addExtension(new ValidatorExtension(Validation::createValidator()))
            ->getFormFactory();
    }

    public function testCreatePeriod(): void
    {
        $builder = $this->getFormFactory()->createBuilder(PeriodType::class, null, [
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

        /** @var \DateTimeImmutable $startDate */
        $startDate = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-01 00:00:00');
        /** @var \DateTimeImmutable $endDate */
        $endDate = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-02 00:00:00');
        self::assertEquals(
            $data,
            Period::fromDatepoint(
                $startDate,
                $endDate,
                Period::INCLUDE_START_EXCLUDE_END
            )
        );
    }

    public function testCreateInvalidPeriod(): void
    {
        $builder = $this->getFormFactory()->createBuilder(PeriodType::class, null, [
            'start_date_options' => [
                'widget' => 'single_text',
            ],
            'end_date_options' => [
                'widget' => 'single_text',
            ],
        ]);
        $form = $builder->getForm();

        $form->submit([
            'start' => '2020-01-02T00:00:00',
            'end' => '2020-01-01T00:00:00',
        ]);

        $errors = $form->get('end')->getErrors();
        self::assertCount(1, $errors);
    }

    public function testCreateNull(): void
    {
        $builder = $this->getFormFactory()->createBuilder(PeriodType::class, null, [
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
        /** @var \DateTimeImmutable $startDate */
        $startDate = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-01 00:00:00');
        /** @var \DateTimeImmutable $endDate */
        $endDate = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-02 00:00:00');
        $boundaryType = Period::INCLUDE_START_EXCLUDE_END;

        $period = Period::fromDatepoint(
            $startDate,
            $endDate,
            $boundaryType
        );
        $builder = $this->getFormFactory()->createBuilder(
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
        /** @var \DateTimeImmutable $startDate */
        $startDate = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-01 00:00:00');
        /** @var \DateTimeImmutable $endDate */
        $endDate = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-01-02 00:00:00');
        $boundaryType = Period::INCLUDE_START_EXCLUDE_END;

        $period = Period::fromDatepoint(
            $startDate,
            $endDate,
            $boundaryType
        );
        $builder = $this->getFormFactory()->createBuilder(
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
