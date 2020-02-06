<?php
namespace extas\components\quality\crawlers\jira\reactions;

use extas\components\Item;
use extas\interfaces\quality\crawlers\jira\reactions\IJiraReactionConfigItem;

/**
 * Class JiraReactionConfigItem
 *
 * @package extas\components\quality\crawlers\jira\reactions
 * @author jeyroik@gmail.com
 */
class JiraReactionConfigItem extends Item implements IJiraReactionConfigItem
{
    /**
     * @return string
     */
    public function getFrom(): string
    {
        return $this->config[static::FIELD__FROM] ?? '';
    }

    /**
     * @return string
     */
    public function getTo(): string
    {
        return $this->config[static::FIELD__TO] ?? '';
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return static::SUBJECT;
    }
}
