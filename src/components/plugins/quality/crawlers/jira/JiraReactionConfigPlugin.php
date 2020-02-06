<?php
namespace extas\components\plugins\quality\crawlers\jira;

use extas\components\plugins\Plugin;
use extas\interfaces\quality\crawlers\jira\reactions\IJiraReactionConfig as I;

/**
 * Class JiraReactionConfigPlugin
 *
 * @package extas\components\plugins\quality\crawlers\jira
 * @author jeyroik@gmail.com
 */
class JiraReactionConfigPlugin extends Plugin
{
    /**
     * @param array $config
     */
    public function __invoke(array &$config)
    {
        $qConfigPath = getenv('EXTAS__Q_JIRA_REACTION_PATH') ?: '';
        if (is_file($qConfigPath)) {
            $qConfig = include $qConfigPath;
            $config[I::FIELD__REACTION] = $qConfig[I::FIELD__REACTION] ?? [];
        }
    }
}
