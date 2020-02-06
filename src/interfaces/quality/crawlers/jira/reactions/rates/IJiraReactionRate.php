<?php
namespace extas\interfaces\quality\crawlers\jira\reactions\rates;

use extas\interfaces\IItem;

/**
 * Interface IJiraReactionRate
 *
 * @package extas\interfaces\quality\crawlers\jira\reactions\rates
 * @author jeyroik@gmail.com
 */
interface IJiraReactionRate extends IItem
{
    const SUBJECT = 'extas.quality.crawlers.jira.reaction';

    const FIELD__MONTH = 'month';
    const FIELD__TIMESTAMP = 'timestamp';
    const FIELD__RATE = 'rate';

    /**
     * @return int
     */
    public function getMonth(): int;

    /**
     * @return int
     */
    public function getTimestamp(): int;

    /**
     * @return float
     */
    public function getRate(): float;

    /**
     * @param int $month
     *
     * @return IJiraReactionRate
     */
    public function setMonth(int $month): IJiraReactionRate;

    /**
     * @param int $timestamp
     *
     * @return IJiraReactionRate
     */
    public function setTimestamp(int $timestamp): IJiraReactionRate;

    /**
     * @param float $rate
     *
     * @return IJiraReactionRate
     */
    public function setRate(float $rate): IJiraReactionRate;
}
