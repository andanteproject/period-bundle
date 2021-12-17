<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\PropertyAccess;

use Symfony\Component\PropertyAccess\Exception\AccessException;
use Symfony\Component\PropertyAccess\Exception\UninitializedPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess as SfPropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor as SfPropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

class PropertyAccessor implements PropertyAccessorInterface
{
    private SfPropertyAccessor $propertyAccessor;

    public function __construct(SfPropertyAccessor $propertyAccess)
    {
        $this->propertyAccessor = $propertyAccess;
    }

    public static function create(): self
    {
        return new self(SfPropertyAccess::createPropertyAccessor());
    }

    public function setValue(object|array &$objectOrArray, string|PropertyPathInterface $propertyPath, mixed $value): void
    {
        $this->propertyAccessor->setValue($objectOrArray, $propertyPath, $value);
    }

    public function getValue(object|array $objectOrArray, string|PropertyPathInterface $propertyPath): mixed
    {
        return $this->propertyAccessor->getValue($objectOrArray, $propertyPath);
    }

    public function isWritable(object|array $objectOrArray, string|PropertyPathInterface $propertyPath): bool
    {
        return $this->propertyAccessor->isWritable($objectOrArray, $propertyPath);
    }

    public function isReadable(object|array $objectOrArray, string|PropertyPathInterface $propertyPath): bool
    {
        return $this->propertyAccessor->isReadable($objectOrArray, $propertyPath);
    }

    public function isUninitialized(object|array $objectOrArray, string|PropertyPathInterface $propertyPath): bool
    {
        try {
            $this->propertyAccessor->getValue($objectOrArray, $propertyPath);
        } catch (AccessException $e) {
            if (!$e instanceof UninitializedPropertyException &&
                (
                    \class_exists(UninitializedPropertyException::class) ||
                    \str_contains('You should initialize it', $e->getMessage())
                )
            ) {
                throw $e;
            }

            return true;
        }

        return false;
    }
}
