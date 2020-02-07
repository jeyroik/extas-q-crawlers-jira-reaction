<?php
namespace extas\interfaces\quality\crawlers\jira\reactions\rates;

use extas\interfaces\IItem;
use extas\interfaces\quality\crawlers\jira\IHasMonth;
use extas\interfaces\quality\crawlers\jira\IHasRate;
use extas\interfaces\quality\crawlers\jira\IHasTimestamp;

/**
 * Interface IJiraReactionRate
 *
 * @package extas\interfaces\quality\crawlers\jira\reactions\rates
 * @author jeyroik@gmail.com
 */
interface IJiraReactionRate extends IItem, IHasMonth, IHasTimestamp, IHasRate
{
    const SUBJECT = 'extas.quality.crawlers.jira.reaction';

    const FIELD__COUNT_TOTAL = 'total';

    /**
     * @return int
     */
    public function getCountTotal(): int;

    /**
     * @param int $countTotal
     *
     * @return IJiraReactionRate
     */
    public function setCountTotal(int $countTotal): IJiraReactionRate;
}
