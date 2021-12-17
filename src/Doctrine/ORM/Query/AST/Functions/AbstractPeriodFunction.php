<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Doctrine\ORM\Query\AST\Functions;

use Andante\PeriodBundle\Exception\ParseException;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\PathExpression;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use League\Period\Period;

abstract class AbstractPeriodFunction extends FunctionNode
{
    private PathExpression $fieldPathExpression;

    private bool $embedded = false;

    public const NAME = '*';

    abstract protected function getPropertyName(): string;

    public function getSql(SqlWalker $sqlWalker): string
    {
        if ($this->embedded) {
            return $this->fieldPathExpression->dispatch($sqlWalker);
        }

        // Period in JSON format
        return \sprintf(
            'JSON_UNQUOTE(JSON_EXTRACT(%s,\'$.%s\'))',
            $this->fieldPathExpression->dispatch($sqlWalker),
            $this->getPropertyName()
        );
    }

    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->fieldPathExpression = $parser->StateFieldPathExpression();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);

        if (null === $this->fieldPathExpression->field) {
            throw new ParseException(\sprintf('"%s" DQL function must be used on a single field path', static::NAME));
        }

        $query = $this->getQueryFromParser($parser);
        $dql = $this->getQueryFromParser($parser)->getDQL();

        if (null === $dql) {
            throw new ParseException(\sprintf('Cannot read DQL while using "%1$s" DQL function. Please use EMBEDDED_%1$s and JSON_%1$s functions based on your mapping instead.', static::NAME));
        }

        $entity = $this->getEntityClassByAlias(
            $dql,
            $this->fieldPathExpression->identificationVariable
        );

        if ($this->isEmbeddedPeriodPropertyPath(
            $query->getEntityManager()->getClassMetadata($entity),
            $this->fieldPathExpression->field
        )) {
            // It's embedded, we need to change fieldPathExpression in order to let Doctrine work in the right way
            $this->embedded = true;
            $this->fieldPathExpression->field .= \sprintf('.%s', $this->getPropertyName());
        }
    }

    private function getQueryFromParser(Parser $parser): Query
    {
        $rpParserQuery = new \ReflectionProperty(Parser::class, 'query');
        // I'm so sorry Doctrine... So sorry. (∩ ͡ ° ʖ ͡ °) ⊃-(===>
        $rpParserQuery->setAccessible(true);
        $query = $rpParserQuery->getValue($parser);
        $rpParserQuery->setAccessible(false);
        // Shhhh. It's all right, Doctrine... Nothing happened... It's all right... ( ಠ ͜ʖಠ)
        return $query;
    }

    private function isEmbeddedPeriodPropertyPath(ClassMetadata $classMetadata, string $field): bool
    {
        return
            isset($classMetadata->embeddedClasses[$field]) &&
            Period::class === $classMetadata->embeddedClasses[$field]['class'];
    }

    private function getEntityClassByAlias(string $dql, string $identificationVariable): string
    {
        $matches = [];
        \preg_match(
            \sprintf(
                '/(?P<entity>(?:\\\\{1,2}\w+|\w+\\\\{1,2})(?:\w+\\\\{0,2})+) \b(%s)\b/',
                $identificationVariable
            ),
            $dql,
            $matches
        );
        if (!isset($matches['entity']) || !\is_string($matches['entity'])) {
            throw new ParseException(\sprintf('Cannot auto identify automatically which DQL function to use to access %1$s period property. Please use EMBEDDED_%2$s and JSON_%2$s functions based on your mapping instead.', $this->getPropertyName(), static::NAME));
        }

        return $matches['entity'];
    }
}
