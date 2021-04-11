<?php

declare(strict_types=1);

namespace Andante\PeriodBundle\Doctrine\ORM\Query;

class PeriodExpr
{
    /**
     * TODO:
     * @param mixed      $val Valued to be inspected if during period.
     * @param int|string $x
     *
     * Tells whether an instance is fully contained in the specified interval.
     *
     *     [----------)
     * [--------------------)
     */
    public static function isDuring($val, $x): string
    {
        return 'PERIOD_IS_DURING(' . $val . ' ,' . $x . ' ) = 1';
    }

    /**
     * TODO:
     * Tells whether an instance is entirely before the specified index.
     *
     * The index can be a DateTimeInterface object or another Period object.
     *
     * [--------------------)
     *                          [--------------------)
     *
     * @param mixed $index a datepoint or a Period object
     */
    //abstract public function isBefore($index): bool;

    /**
     * TODO:
     * Tells whether the current instance end date meets the interval start date.
     *
     * [--------------------)
     *                      [--------------------)
     */
    //abstract public function bordersOnStart(self $interval): bool;

    /**
     * TODO:
     * Tells whether two intervals share the same start datepoint
     * and the same starting boundary type.
     *
     *    [----------)
     *    [--------------------)
     *
     * or
     *
     *    [--------------------)
     *    [---------)
     *
     * @param mixed $index a datepoint or a Period object
     */
    //abstract public function isStartedBy($index): bool;

    /**
     * TODO:
     * Tells whether an instance fully contains the specified index.
     *
     * The index can be a DateTimeInterface object or another Period object.
     *
     * @param mixed $index a datepoint or a Period object
     */
    //abstract public function contains($index): bool;

    /**
     * Tells whether two intervals share the same datepoints.
     *
     * [--------------------)
     * [--------------------)
     */
    //abstract public function equals(self $interval): bool;

    /**
     * TODO:
     * Tells whether two intervals share the same end datepoint
     * and the same ending boundary type.
     *
     *              [----------)
     *    [--------------------)
     *
     * or
     *
     *    [--------------------)
     *               [---------)
     *
     * @param mixed $index a datepoint or a Period object
     */
    //abstract public function isEndedBy($index): bool;

    /**
     * TODO:
     * Tells whether the current instance start date meets the interval end date.
     *
     *                      [--------------------)
     * [--------------------)
     */
    //abstract public function bordersOnEnd(self $interval): bool;

    /**
     * TODO:
     * Tells whether an interval is entirely after the specified index.
     * The index can be a DateTimeInterface object or another Period object.
     *
     *                          [--------------------)
     * [--------------------)
     *
     * @param mixed $index a datepoint or a Period object
     */
    //abstract public function isAfter($index): bool;

    /**
     * TODO:
     * Tells whether two intervals abuts.
     *
     * [--------------------)
     *                      [--------------------)
     * or
     *                      [--------------------)
     * [--------------------)
     */
    //abstract public function abuts(self $interval): bool;

    /**
     * TODO:
     * Tells whether two intervals overlaps.
     *
     * [--------------------)
     *          [--------------------)
     */
    //abstract public function overlaps(self $interval): bool;
}
