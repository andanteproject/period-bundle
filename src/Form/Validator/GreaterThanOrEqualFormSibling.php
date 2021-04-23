<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Form\Validator;

use Composer\Semver\Constraint\Constraint;
use Symfony\Component\Validator\Constraints\AbstractComparison;

class GreaterThanOrEqualFormSibling extends AbstractComparison
{
    /** @var string */
    public $message = 'This value should be greater than or equal to {{ compared_value }}.';
}
