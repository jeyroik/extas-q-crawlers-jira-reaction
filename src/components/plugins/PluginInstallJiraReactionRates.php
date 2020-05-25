<?php
namespace extas\components\plugins;

use extas\components\quality\crawlers\jira\reactions\rates\JiraReactionRate;
use extas\interfaces\quality\crawlers\jira\reactions\rates\IJiraReactionRateRepository;

/**
 * Class PluginInstallJiraReactionRates
 *
 * @package extas\components\plugins
 * @author jeyroik@gmail.com
 */
class PluginInstallJiraReactionRates extends PluginInstallDefault
{
    protected string $selfUID = JiraReactionRate::FIELD__MONTH;
    protected string $selfRepositoryClass = IJiraReactionRateRepository::class;
    protected string $selfSection = 'jira_reaction_rates';
    protected string $selfName = 'jira reaction rate';
    protected string $selfItemClass = JiraReactionRate::class;
}
