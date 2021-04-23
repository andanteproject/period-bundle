<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Form\Validator;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\AbstractComparisonValidator;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class GreaterThanOrEqualFormChildrenValidator extends AbstractComparisonValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($data, Constraint $constraint): void
    {
        if (!$constraint instanceof GreaterThanOrEqualFormChildren) {
            throw new UnexpectedTypeException($constraint, GreaterThanOrEqualFormChildren::class);
        }

        $object = $this->context->getObject();
        if (!$object instanceof FormInterface) {
            throw new ConstraintDefinitionException(sprintf('"%s" constraint should only be used in Forms ', get_debug_type($constraint)));
        }

        $form = $constraint->useParent ? $object->getParent() : $object;

        if (null === $form) {
            throw new ConstraintDefinitionException(sprintf('"%s" cannot be used on root forms', get_debug_type($constraint)));
        }

        if (!$form->has($constraint->child)) {
            throw new ConstraintDefinitionException(sprintf('"%s" cannot find child "%s" on form', get_debug_type($constraint), $constraint->child));
        }

        if (!$form->has($constraint->gteChild)) {
            throw new ConstraintDefinitionException(sprintf('"%s" cannot find child "%s" on form', get_debug_type($constraint), $constraint->gteChild));
        }

        $comparedValue = $form->get($constraint->gteChild)->getData();
        $value = $form->get($constraint->child)->getData();

        if (null === $value || null === $comparedValue) {
            return;
        }

        // Convert strings to DateTimes if comparing another DateTime
        // This allows to compare with any date/time value supported by
        // the DateTime constructor:
        // https://php.net/datetime.formats
        if (\is_string($comparedValue) && $value instanceof \DateTimeInterface) {
            // If $value is immutable, convert the compared value to a DateTimeImmutable too, otherwise use DateTime
            $dateTimeClass = $value instanceof \DateTimeImmutable ? \DateTimeImmutable::class : \DateTime::class;

            try {
                $comparedValue = new $dateTimeClass($comparedValue);
            } catch (\Exception $e) {
                throw new ConstraintDefinitionException(sprintf('The compared value "%s" could not be converted to a "%s" instance in the "%s" constraint.', $comparedValue, $dateTimeClass, get_debug_type($constraint)));
            }
        }

        if (!$this->compareValues($value, $comparedValue)) {
            $violationBuilder = $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value, self::OBJECT_TO_STRING | self::PRETTY_DATE))
                ->setParameter('{{ compared_value }}', $this->formatValue($comparedValue, self::OBJECT_TO_STRING | self::PRETTY_DATE))
                ->setParameter('{{ compared_value_type }}', $this->formatTypeOf($comparedValue))
                ->setCode($this->getErrorCode());

            $violationBuilder->addViolation();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function compareValues($value1, $value2): bool
    {
        return null === $value2 || $value1 >= $value2;
    }

    /**
     * {@inheritdoc}
     */
    protected function getErrorCode(): string
    {
        return GreaterThanOrEqual::TOO_LOW_ERROR;
    }
}
