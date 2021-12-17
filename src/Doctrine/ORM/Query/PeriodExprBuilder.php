<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Doctrine\ORM\Query;

use Andante\PeriodBundle\Doctrine\DBAL\Type\PeriodType;
use Andante\PeriodBundle\Doctrine\ORM\Query\AST\Functions\EmbeddedPeriodBoundaryType;
use Andante\PeriodBundle\Doctrine\ORM\Query\AST\Functions\EmbeddedPeriodEndDate;
use Andante\PeriodBundle\Doctrine\ORM\Query\AST\Functions\EmbeddedPeriodStartDate;
use Andante\PeriodBundle\Doctrine\ORM\Query\AST\Functions\JsonPeriodBoundaryType;
use Andante\PeriodBundle\Doctrine\ORM\Query\AST\Functions\JsonPeriodEndDate;
use Andante\PeriodBundle\Doctrine\ORM\Query\AST\Functions\JsonPeriodStartDate;
use Andante\PeriodBundle\Exception\InvalidPeriodPathExpression;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\QueryBuilder;
use League\Period\Period;

class PeriodExprBuilder
{
    private QueryBuilder $qb;

    private function __construct(QueryBuilder $qb)
    {
        $this->qb = $qb;
    }

    public static function create(QueryBuilder $qb): self
    {
        return new self($qb);
    }

    public function getStartDate(string $periodPropertyPath): string
    {
        return \sprintf(
            '%s(%s)',
            $this->isEmbeddedPeriodPropertyPath($periodPropertyPath) ?
                EmbeddedPeriodStartDate::NAME : JsonPeriodStartDate::NAME,
            $periodPropertyPath
        );
    }

    public function getEndDate(string $periodPropertyPath): string
    {
        return \sprintf(
            '%s(%s)',
            $this->isEmbeddedPeriodPropertyPath($periodPropertyPath) ?
                EmbeddedPeriodEndDate::NAME : JsonPeriodEndDate::NAME,
            $periodPropertyPath
        );
    }

    public function getBoundaryType(string $periodPropertyPath): string
    {
        return \sprintf(
            '%s(%s)',
            $this->isEmbeddedPeriodPropertyPath($periodPropertyPath) ?
                EmbeddedPeriodBoundaryType::NAME : JsonPeriodBoundaryType::NAME,
            $periodPropertyPath
        );
    }

    protected function isEmbeddedPeriodPropertyPath(string $singlePropertyPath): bool
    {
        $matches = [];
        if (\preg_match(
            '/(?P<alias>'.\implode('|', $this->qb->getAllAliases()).')\./',
            $singlePropertyPath,
            $matches
        )) {
            if (isset($matches['alias']) && \is_string($matches['alias'])) {
                $alias = $matches['alias'];
                $rootEntity = $this->getEntityForAlias($alias);
                if (null !== $rootEntity) {
                    $rootClassMetadata = $this->qb->getEntityManager()->getClassMetadata($rootEntity);
                    $fieldName = \preg_replace('/^'.$alias.'\./', '', $singlePropertyPath);
                    if (\is_string($fieldName)) {
                        if (isset($rootClassMetadata->embeddedClasses[$fieldName]) && Period::class === $rootClassMetadata->embeddedClasses[$fieldName]['class']) {
                            return true;
                        }
                        try {
                            $fieldMapping = $rootClassMetadata->getFieldMapping($fieldName);
                            if (isset($fieldMapping['type']) && PeriodType::NAME === $fieldMapping['type']) {
                                return false;
                            }
                        } catch (MappingException $e) {
                            throw new InvalidPeriodPathExpression(\sprintf('Path "%s" is not a %s property', $singlePropertyPath, Period::class), 0, $e);
                        }
                    }
                }
            }
        }
        throw new InvalidPeriodPathExpression(\sprintf('Path "%s" is not a Period property', $singlePropertyPath));
    }

    private function getEntityForAlias(string $alias): ?string
    {
        $entityIndex = \array_search($alias, $this->qb->getRootAliases(), true);
        if (\is_numeric($entityIndex)) {
            return $this->qb->getRootEntities()[$entityIndex];
        }

        return null;
    }
}
