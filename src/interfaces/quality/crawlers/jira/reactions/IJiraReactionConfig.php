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
    public const FIELD__REACTION = 'reaction';
    public const FIELD__PROJECTS = 'projects';
    public const FIELD__IN_WORK = 'in_work';
    public const FIELD__RESOLVED = 'resolved';
    public const FIELD__FROM = 'from';
    public const FIELD__TO = 'to';
}
