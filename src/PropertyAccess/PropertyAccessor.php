<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\PropertyAccess;

use Symfony\Component\PropertyAccess\Exception\AccessException;
use Symfony\Component\PropertyAccess\Exception\UninitializedPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess as SfPropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor as SfPropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

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

    /**
     * @param array|object                                                   $objectOrArray
     * @param string|\Symfony\Component\PropertyAccess\PropertyPathInterface $propertyPath
     * @param mixed                                                          $value
     */
    public function setValue(&$objectOrArray, $propertyPath, $value): void
    {
        $this->propertyAccessor->setValue($objectOrArray, $propertyPath, $value);
    }

    /**
     * @param array|object                                                   $objectOrArray
     * @param string|\Symfony\Component\PropertyAccess\PropertyPathInterface $propertyPath
     *
     * @return mixed|null
     */
    public function getValue($objectOrArray, $propertyPath)
    {
        return $this->propertyAccessor->getValue($objectOrArray, $propertyPath);
    }

    /**
     * @param array|object                                                   $objectOrArray
     * @param string|\Symfony\Component\PropertyAccess\PropertyPathInterface $propertyPath
     *
     * @return bool
     */
    public function isWritable($objectOrArray, $propertyPath): bool
    {
        return $this->propertyAccessor->isWritable($objectOrArray, $propertyPath);
    }

    /**
     * @param array|object                                                   $objectOrArray
     * @param string|\Symfony\Component\PropertyAccess\PropertyPathInterface $propertyPath
     *
     * @return bool
     */
    public function isReadable($objectOrArray, $propertyPath): bool
    {
        return $this->propertyAccessor->isReadable($objectOrArray, $propertyPath);
    }

    /**
     * @param array|object                                                   $objectOrArray
     * @param string|\Symfony\Component\PropertyAccess\PropertyPathInterface $propertyPath
     *
     * @return bool
     */
    public function isUninitialized($objectOrArray, $propertyPath): bool
    {
        try {
            $this->propertyAccessor->getValue($objectOrArray, $propertyPath);
        } catch (AccessException $e) {
            if (! $e instanceof UninitializedPropertyException &&
                (
                    class_exists(UninitializedPropertyException::class) ||
                    str_contains('You should initialize it', $e->getMessage())
                )
            ) {
                throw $e;
            }

            return true;
        }
        return false;
    }
}
