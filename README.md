![Andante Project Logo](https://github.com/andanteproject/period-bundle/blob/main/andanteproject-logo.png?raw=true)

# Period Bundle

#### Symfony Bundle - [AndanteProject](https://github.com/andanteproject)

[![Latest Version](https://img.shields.io/github/release/andanteproject/period-bundle.svg)](https://github.com/andanteproject/period-bundle/releases)
![Github actions](https://github.com/andanteproject/period-bundle/actions/workflows/workflow.yml/badge.svg?branch=main)
![Framework](https://img.shields.io/badge/Symfony-5.4|6.x-informational?Style=flat&logo=symfony)
![Php7](https://img.shields.io/badge/PHP-%208.x-informational?style=flat&logo=php)
![PhpStan](https://img.shields.io/badge/PHPStan-Level%208-syccess?style=flat&logo=php)

A Symfony Bundle to integrate [thephpleague/period](https://period.thephpleague.com)
into [Doctrine](https://github.com/doctrine/DoctrineBundle) and [Symfony Form](https://github.com/symfony/form).

## Requirements

Symfony 5.4-6.x and PHP 8.0.

## Install

Via [Composer](https://getcomposer.org/):

```bash
$ composer require andanteproject/period-bundle
```

## Features

- Persist `Period`, `Duration` and `Sequence` on your DB;
- Persist `Period` as a JSON field or
  a [doctrine embeddable object](https://www.doctrine-project.org/projects/doctrine-orm/en/2.8/tutorials/embeddables.html#separating-concerns-using-embeddables)
  effortless (and it is allowed to be `null`!!).
- Doctrine DQL functions.
- Use `Period` in Symfony Forms its Form Type;
- Works like magic ✨.

## Basic usage

After [install](#install), make sure you have the bundle registered in your symfony bundles list (`config/bundles.php`):

```php
return [
    /// bundles...
    Andante\PeriodBundle\AndantePeriodBundle::class => ['all' => true],
    /// bundles...
];
```

This should have been done automagically if you are using [Symfony Flex](https://flex.symfony.com). Otherwise, just
register it by yourself.

## Doctrine Mapping

The bundle is going to register `period`, `duration` and `sequence` doctrine types to allow you to map `Period`
, `Duration` and `Sequence` objects to the database.

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use League\Period\Duration;
use League\Period\Period;
use League\Period\Sequence;

/**
 * @ORM\Entity()
 */
class Meeting
{
    /**
     * @ORM\Column(type="period", nullable=true)
     */
    private ?Period $period = null;
    
    /**
     * @ORM\Column(type="duration", nullable=true)
     */
    private ?Duration $duration = null;
    
    /**
     * @ORM\Column(type="sequence", nullable=true)
     */
    private ?Sequence $sequence = null;
}
```

These types are going to create a `JSON` field on your database. If you want `Period` to have a column for `startDate`
and a separate column for `endDate`, check the [Embeddable mapping](#embeddable-period-mapping) down below.

### Embeddable Period Mapping

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use League\Period\Period;

/**
 * @ORM\Entity()
 */
class Meeting
{
    /**
     * @ORM\Embedded(class="League\Period\Period", columnPrefix="period_")
     */
    private ?Period $period = null;
}
```

This is going to create 3 different columns on your database like `period_start_date`, `period_end_date`
and `period_boundary_type` instead of a JSON field. If you want to use some different names for yout mapping, check
the [configuration](#configuration-completely-optional) of this bundle. ⚠️ **PLEASE NOTE:** Doctrine v2 does not allow
Embedded Classes to be `null`. It's a feature expected in Doctrine v3. **But**, with some magic under the hood, this
bundle allows you to use `nullable` `Period` anyway. 👍

## Doctrine DQL Functions

No matter the kind of mapping you are using for your Period ([type](#doctrine-mapping)
or [embedded](#embeddable-period-mapping)), you can use these DQL functions to access Period properties:

- `PERIOD_START_DATE()` to access `period.startDate`, like `PERIOD_START_DATE(meeting.period)`;
- `PERIOD_END_DATE()` to access `period.endDate`, like `PERIOD_END_DATE(meeting.period)`;
- `PERIOD_BOUNDARY_TYPE()` to access `period.boundaryType`, like `PERIOD_BOUNDARY_TYPE(meeting.period)`.

## Period Form Type

Use `Andante\PeriodBundle\Form\PeriodType` as a Form like you are used to. This bundle is shipped with no form theme, so
it's up to you to build your form theme.

```php
<?php

declare(strict_types=1);

use Andante\PeriodBundle\Form\PeriodType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', Type\TextType::class)
            ->add('period', PeriodType::class)
        ;
    }
}
```

### PeriodType Options

#### default_boundary_type

**type**: `string` **default**: `[)`, allowed values: `[)`, `(]`, `()`, `[]`
which boundary type to be used if none has been selected via `boundary_type_choice`.

```php
$builder->add('period', PeriodType::class, [
    'default_boundary_type' => '()',
]);
```

#### boundary_type_choice

**type**: `bool` **default**: `false`
Whether to include or not a `BoundaryTypeChoiceType` to let the user to choice the BoundaryType. This is `false` by
default. To change which boundary type should be use to create the `Period`, check out `default_boundary_type` option.

```php
$builder->add('period', PeriodType::class, [
    'boundary_type_choice' => true,
]);
```

#### start_date_child_name

**type**: `string` **default**: `start`
How form child handling `startDate` property should be called.

```php
$builder->add('period', PeriodType::class, [
    'start_date_child_name' => 'custom_start_date_form_child_name',
]);
```

#### end_date_child_name

**type**: `string` **default**: `end`
How form child handling `endDate` property should be called.

```php
$builder->add('period', PeriodType::class, [
    'end_date_child_name' => 'custom_end_date_form_child_name',
]);
```

#### boundary_type_child_name

**type**: `string` **default**: `boundary`

```php
$builder->add('period', PeriodType::class, [
    'boundary_type_child_name' => 'custom_boundary_type_form_child_name',
]);
```

How form child handling `boundaryType` property should be called.

#### start_date_form_type

**type**: `string` **default**: `Symfony\Component\Form\Extension\Core\Type\DateTimeType`
Which form type to be used for `startDate` property. You can replace it with something custom.

```php
use App\Form\MyDateTimeType;

$builder->add('period', PeriodType::class, [
    'start_date_form_type' => MyDateTimeType::class,
]);
```

#### end_date_form_type

**type**: `string` **default**: `Symfony\Component\Form\Extension\Core\Type\DateTimeType`
Which form type to be used for `endDate` property. You can replace it with something custom.

```php
use App\Form\MyDateTimeType;

$builder->add('period', PeriodType::class, [
    'end_date_form_type' => MyDateTimeType::class,
]);
```

#### start_date_options

**type**: `array` **default**: `[]`
Additional options to be used for the *startDate* form child.

```php
$builder->add('period', PeriodType::class, [
    'start_date_options' => [
        'label' => 'A different Label',
        // + whatever option allowed by DateTimeType
    ],
]);
```

#### end_date_options

**type**: `array` **default**: `[]`
Additional options to be used for the *endDate* form child.

```php
$builder->add('period', PeriodType::class, [
    'end_date_options' => [
        'label' => 'A different Label',
        // + whatever option allowed by DateTimeType
    ],
]);
```

#### boundary_type_options

**type**: `array` **default**: `[]`
Additional options to be used for the *boundaryType* form child.

```php
$builder->add('period', PeriodType::class, [
    'boundary_type_options' => [
        'label' => 'A different Label',
        // + whatever option allowed by Andante\PeriodBundle\Form\BoundaryTypeChoiceType
    ],
]);
```

#### allow_null

**type**: `bool` **default**: `true`
Additional options to be used for the *boundaryType* form child.

```php
$builder->add('period', PeriodType::class, [
    'allow_null' => false,
    // Allow to trigger an error when your Period property is not nullable.
]);
```

## Configuration (completely optional)

This bundle is build thinking how to save you time and follow best practices as close as possible.

This means you can even ignore to have a `andante_period.yml` config file in your application.

However, for whatever reason, use the bundle configuration to change most of the behaviors as your needs.

```yaml
andante_period:
  doctrine:
    embedded_period:
      default:
        start_date_column_name: start_date # default: null
          # Column name to be used on database for startDate property. 
        # If set to NULL will use your default doctrine naming strategy
        end_date_column_name: end_date # default: null
          # Column name to be used on database for endDate property. 
        # If set to NULL will use your default doctrine naming strategy
        boundary_type_column_name: boundary_type # default: null
          # Column name to be used on database for update boundaryType property. 
        # If set to NULL will use your default doctrine naming strategy
      entity: # You can use per-entity configuration to override default config
        App\Entity\Event:
          start_date_column_name: starting_at
          end_date_column_name: ending_at
        App\Entity\Meeting:
          start_date_column_name: start
          end_date_column_name: end
```

Built with love ❤️ by [AndanteProject](https://github.com/andanteproject) team.
