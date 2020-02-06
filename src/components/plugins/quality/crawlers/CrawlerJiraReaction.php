<?php
namespace extas\components\plugins\quality\crawlers;

use extas\components\quality\crawlers\Crawler;
use extas\components\quality\crawlers\jira\JiraClient;
use extas\components\quality\crawlers\jira\JiraSearchJQL;
use extas\components\quality\crawlers\jira\reactions\JiraReactionConfigItem;
use extas\components\quality\crawlers\jira\reactions\rates\JiraReactionRate;
use extas\components\quality\crawlers\jira\TJiraConfiguration;
use extas\components\SystemContainer;
use extas\interfaces\quality\crawlers\ICrawler;
use extas\interfaces\quality\crawlers\jira\IJiraIssue;
use extas\interfaces\quality\crawlers\jira\IJiraIssueChangelog;
use extas\interfaces\quality\crawlers\jira\IJiraSearchJQL;
use extas\interfaces\quality\crawlers\jira\reactions\IJiraReactionConfig as I;
use extas\interfaces\quality\crawlers\jira\reactions\IJiraReactionConfigItem;
use extas\interfaces\quality\crawlers\jira\reactions\rates\IJiraReactionRateRepository;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CrawlerJiraReaction
 *
 * @package extas\components\plugins\quality\crawlers
 * @author jeyroik@gmail.com
 */
class CrawlerJiraReaction extends Crawler
{
    use TJiraConfiguration;

    protected $title = '[Jira] Reaction index';
    protected $description = 'Calculate reaction index.';

    /**
     * @param OutputInterface $output
     *
     * @return ICrawler
     */
    public function __invoke(OutputInterface &$output): ICrawler
    {
        try {
            $jiraClient = new JiraClient();
            $inWork = $this->getInWorkSettings();
            $resolved = $this->getResolvedSettings();
            $inWorkTimes = [];
            $resolvedTimes = [];
            $itemsCount = 0;
            $notResolved = 0;

            foreach ($jiraClient->allTickets($this->getJql()) as $ticket) {
                /**
                 * @var $ticket IJiraIssue
                 */
                $output->writeln(['Operating ticket <info>' . $ticket->getKey() . '</info>']);
                $created = $ticket->getCreated(true);
                $changeLog = $ticket->getChangelog();
                $this->inWorkTimes($inWorkTimes, $created, $inWork, $changeLog);
                $this->resolvedTimes($resolvedTimes, $notResolved, $created, $resolved, $changeLog);
                $itemsCount++;
            }
            $rate = round(1 / (
                1 + $this->median($inWorkTimes) + $this->median($resolvedTimes) + $itemsCount + $notResolved
            ),4);

            $output->writeln([
                'Rate = 1 / (1 + ' . $this->median($inWorkTimes) . ' (reaction time) + ' .
                $this->median($resolvedTimes) . ' (resolving time) + ' .
                $itemsCount . ' (issue count) + ' . $notResolved . ' (not resolved) )',
                'Rate =  <info>' . $rate . '</info>'
            ]);
            /**
             * @var $repo IJiraReactionRateRepository
             */
            $repo = SystemContainer::getItem(IJiraReactionRateRepository::class);
            $repo->create(new JiraReactionRate([
                JiraReactionRate::FIELD__MONTH =>date('Ym'),
                JiraReactionRate::FIELD__TIMESTAMP => time(),
                JiraReactionRate::FIELD__RATE => $rate
            ]));
            return $this;

        } catch (\Exception $e) {
            $messages = explode('\n', $e->getMessage());
            $output->writeln($messages);
            return $this;
        }
    }

    /**
     * @return IJiraSearchJQL
     * @throws \Exception
     */
    protected function getJql(): IJiraSearchJQL
    {
        $jql = new JiraSearchJQL();
        $jql->updatedDate(
            JiraSearchJQL::CONDITION__LOWER,
            JiraSearchJQL::DATE__END_OF_MONTH,
            -1
        );
        
        $config = $this->cfg();
        if (isset($config[I::FIELD__REACTION])) {
            $r = $config[I::FIELD__REACTION];
            if (isset($r[I::FIELD__PROJECTS]) && !empty($r[I::FIELD__PROJECTS])) {
                $jql->projectKey($r[I::FIELD__PROJECTS]);
            }
        }

        $jql->returnFields([IJiraIssue::FIELD__CHANGELOG]);

        return $jql;
    }

    /**
     * @param array $times
     * @param int $notR
     * @param int $created
     * @param IJiraReactionConfigItem $resolved
     * @param $changeLog
     */
    protected function resolvedTimes(
        array &$times,
        int &$notR,
        int $created,
        IJiraReactionConfigItem $resolved,
        IJiraIssueChangelog $changeLog
    )
    {
        $item = $changeLog->one($resolved->getFrom(), $resolved->getTo());
        if ($item) {
            $doneTime = $item->getCreated(true);
            $times[] = $doneTime - $created;
        } else {
            $notR++;
        }
    }

    /**
     * @param array $inWorkTimes
     * @param int $created
     * @param IJiraReactionConfigItem $inWork
     * @param IJiraIssueChangelog $changeLog
     */
    protected function inWorkTimes(
        array &$inWorkTimes,
        int $created,
        IJiraReactionConfigItem $inWork,
        IJiraIssueChangelog $changeLog
    )
    {
        $item = $changeLog->one($inWork->getFrom(), $inWork->getTo());

        if ($item) {
            $inWorkTime = $item->getCreated(true);
            $inWorkTimes[] = $inWorkTime - $created;
        }
    }

    /**
     * @return IJiraReactionConfigItem
     * @throws \Exception
     */
    protected function getResolvedSettings(): IJiraReactionConfigItem
    {
        $cfg = $this->cfg();
        $reaction = isset($cfg[I::FIELD__REACTION]) ? $cfg[I::FIELD__REACTION] : [];

        return new JiraReactionConfigItem($reaction[I::FIELD__RESOLVED] ?? []);
    }

    /**
     * @return IJiraReactionConfigItem
     * @throws \Exception
     */
    protected function getInWorkSettings(): IJiraReactionConfigItem
    {
        $cfg = $this->cfg();
        $reaction = isset($cfg[I::FIELD__REACTION]) ? $cfg[I::FIELD__REACTION] : [];

        return new JiraReactionConfigItem($reaction[I::FIELD__IN_WORK] ?? []);
    }

    /**
     * @param array $values
     *
     * @return float|int
     */
    protected function median(array $values)
    {
        sort($values);
        $count = count($values);
        $middle = (int) floor($count/2);
        if (!$count) {
            return 0;
        } elseif ($count & 1) {    // count is odd
            return $values[$middle];
        } else {                   // count is even
            return ($values[$middle-1] + $values[$middle])/2;
        }
    }
}
