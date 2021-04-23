<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Form\Validator;

use Andante\PeriodBundle\Exception\InvalidArgumentException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\AbstractComparison;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqualValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class GreaterThanOrEqualFormSiblingValidator extends GreaterThanOrEqualValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint) : void
    {
        if (! $constraint instanceof AbstractComparison) {
            throw new UnexpectedTypeException($constraint, AbstractComparison::class);
        }

        if (null === $value) {
            return;
        }

        if ($path = $constraint->propertyPath) {
            if (null === $object = $this->context->getObject()) {
                return;
            }

            if (! $object instanceof FormInterface) {
                throw new ConstraintDefinitionException(sprintf('%s should be used only on forms', static::class));
            }

            $form = $object->getParent();
            if (! $form instanceof FormInterface) {
                throw new ConstraintDefinitionException(sprintf('%s cannot be used on a form root', static::class));
            }
            if (! $form->has($path)) {
                throw new ConstraintDefinitionException(sprintf('%s cannot find form child "%s"', static::class, $path));
            }

            $comparedValue = $form->get($path)->getData();
        } else {
            $comparedValue = $constraint->value;
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
                throw new ConstraintDefinitionException(sprintf(
                    'The compared value "%s" could not be converted to a "%s" instance in the "%s" constraint.',
                    $comparedValue,
                    $dateTimeClass,
                    get_debug_type($constraint)
                ));
            }
        }

        if (! $this->compareValues($value, $comparedValue)) {
            $violationBuilder = $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value, self::OBJECT_TO_STRING | self::PRETTY_DATE))
                ->setParameter(
                    '{{ compared_value }}',
                    $this->formatValue($comparedValue, self::OBJECT_TO_STRING | self::PRETTY_DATE)
                )
                ->setParameter('{{ compared_value_type }}', $this->formatTypeOf($comparedValue))
                ->setCode($this->getErrorCode());

            if (null !== $path) {
                $violationBuilder->setParameter('{{ compared_value_path }}', $path);
            }

            $violationBuilder->addViolation();
        }
    }
}
