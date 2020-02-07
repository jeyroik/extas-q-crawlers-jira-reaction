<?php
namespace extas\components\quality\crawlers\jira\reactions\rates;

use extas\components\Item;
use extas\components\quality\crawlers\jira\THasMonth;
use extas\components\quality\crawlers\jira\THasRate;
use extas\components\quality\crawlers\jira\THasTimestamp;
use extas\interfaces\quality\crawlers\jira\reactions\rates\IJiraReactionRate;

/**
 * Class JiraReactionRate
 *
 * @package extas\components\quality\crawlers\jira\reactions\rates
 * @author jeyroik@gmail.com
 */
class JiraReactionRate extends Item implements IJiraReactionRate
{
    use THasTimestamp;
    use THasMonth;
    use THasRate;

    /**
     * @return int
     */
    public function getCountTotal(): int
    {
        return $this->config[static::FIELD__COUNT_TOTAL] ?? 0;
    }

    /**
     * @param int $countTotal
     *
     * @return IJiraReactionRate
     */
    public function setCountTotal(int $countTotal): IJiraReactionRate
    {
        $this->config[static::FIELD__COUNT_TOTAL] = $countTotal;

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
