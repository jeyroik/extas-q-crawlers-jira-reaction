<?php
namespace extas\interfaces\quality\crawlers\jira\reactions;

/**
 * Interface IJiraReactionConfig
 *
 * @package extas\interfaces\quality\crawlers\jira\reactions
 * @author jeyroik@gmail.com
 */
interface IJiraReactionConfig
{
    const FIELD__REACTION = 'reaction';
    const FIELD__PROJECTS = 'projects';
    const FIELD__IN_WORK = 'in_work';
    const FIELD__RESOLVED = 'resolved';
    const FIELD__FROM = 'from';
    const FIELD__TO = 'to';
}
