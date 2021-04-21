<?php

declare(strict_types=1);


namespace Andante\PeriodBundle\Exception;

use Symfony\Component\HttpKernel\Attribute\ArgumentInterface;

class InvalidArgumentException extends \InvalidArgumentException implements ArgumentInterface
{
}
