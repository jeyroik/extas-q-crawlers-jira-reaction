<?php
namespace extas\interfaces\quality\crawlers\jira\reactions;

use extas\interfaces\IItem;

/**
 * Interface IJiraReactionConfigItem
 *
 * @package extas\interfaces\quality\crawlers\jira\reactions
 * @author jeyroik@gmail.com
 */
interface IJiraReactionConfigItem extends IItem
{
    const SUBJECT = 'extas.quality.crawler.jira.reaction.config.item';

    const FIELD__FROM = 'from';
    const FIELD__TO = 'to';

    /**
     * @return string
     */
    public function getFrom(): string;

    /**
     * @return string
     */
    public function getTo(): string;
}
