<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Form\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

class GreaterThanOrEqualFormChildren extends Constraint
{
    public string $message = 'This value should be greater than or equal to {{ compared_value }}.';
    public string $child = '';
    public string $gteChild = '';
    public bool $useParent = true;

    public function __construct($options, string $message = null, array $groups = null, $payload = null)
    {
        parent::__construct($options, $groups, $payload);

        $this->message = $message ?? $this->message;

        if (empty($this->child)) {
            throw new ConstraintDefinitionException(sprintf(
                'The "%s" constraint requires "child" option to be set.',
                static::class
            ));
        }

        if (empty($this->gteChild)) {
            throw new ConstraintDefinitionException(sprintf(
                'The "%s" constraint requires "$gteChild" option to be set.',
                static::class
            ));
        }
    }
}
