<?php
namespace extas\components\quality\crawlers\jira\reactions\rates;

use extas\components\Item;
use extas\interfaces\quality\crawlers\jira\reactions\rates\IJiraReactionRate;

/**
 * Class JiraReactionRate
 *
 * @package extas\components\quality\crawlers\jira\reactions\rates
 * @author jeyroik@gmail.com
 */
class JiraReactionRate extends Item implements IJiraReactionRate
{
    /**
     * @return int
     */
    public function getMonth(): int
    {
        return $this->config[static::FIELD__MONTH] ?? 0;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->config[static::FIELD__TIMESTAMP] ?? 0;
    }

    /**
     * @return float
     */
    public function getRate(): float
    {
        return (float) ($this->config[static::FIELD__RATE] ?? 0);
    }

    /**
     * @param int $month
     *
     * @return IJiraReactionRate
     */
    public function setMonth(int $month): IJiraReactionRate
    {
        $this->config[static::FIELD__MONTH] = $month;

        return $this;
    }

    /**
     * @param int $timestamp
     *
     * @return IJiraReactionRate
     */
    public function setTimestamp(int $timestamp): IJiraReactionRate
    {
        $this->config[static::FIELD__TIMESTAMP] = $timestamp;

        return $this;
    }

    /**
     * @param float $rate
     *
     * @return IJiraReactionRate
     */
    public function setRate(float $rate): IJiraReactionRate
    {
        $this->config[static::FIELD__RATE] = $rate;

        return $this;
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return static::SUBJECT;
    }
}
