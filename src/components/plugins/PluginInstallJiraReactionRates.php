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
    protected $selfUID = JiraReactionRate::FIELD__MONTH;
    protected $selfRepositoryClass = IJiraReactionRateRepository::class;
    protected $selfSection = 'jira_reaction_rates';
    protected $selfName = 'jira reaction rate';
    protected $selfItemClass = JiraReactionRate::class;
}
