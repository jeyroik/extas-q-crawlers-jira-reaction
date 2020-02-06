<?php
namespace extas\components\plugins\quality\crawlers;

use extas\components\quality\crawlers\jira\JiraClient;
use extas\interfaces\quality\crawlers\ICrawler;
use extas\interfaces\quality\crawlers\jira\IJiraClient;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CrawlerJiraReaction
 *
 * @package extas\components\plugins\quality\crawlers
 * @author jeyroik@gmail.com
 */
class CrawlerJiraReaction extends CrawlerJiraUserQualification
{
    protected $title = '[Jira] Reaction index';
    protected $description = 'Calculate reaction index.';

    public function __invoke(OutputInterface &$output): ICrawler
    {
        try {
            $jiraClient = new JiraClient();
            $reactionProjectKeys = getenv('EXTAS__Q_REACTION_PROJECTS') ?: '';
            if (!$reactionProjectKeys) {
                throw new \Exception(
                    '<comment>Missed jira reaction project keys</comment>' . '\n' .
                    'Please, define <info>EXTAS__Q_REACTION_PROJECTS</info> env param'
                );
            }
            $this->setProjectKeys($jiraClient, $reactionProjectKeys);

        } catch (\Exception $e) {
            $messages = explode('\n', $e->getMessage());
            $output->writeln($messages);
            return $this;
        }
    }

    /**
     * @param IJiraClient $client
     * @param string $keys
     */
    protected function setProjectKeys(IJiraClient &$client, string $keys)
    {
        $keys = explode(',', $keys);
        $client->setProjectKeys($keys);
    }
}
