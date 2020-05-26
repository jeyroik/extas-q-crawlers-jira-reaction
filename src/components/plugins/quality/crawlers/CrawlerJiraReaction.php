<?php
namespace extas\components\plugins\quality\crawlers;

use extas\components\quality\crawlers\Crawler;
use extas\components\quality\crawlers\jira\JiraClient;
use extas\components\quality\crawlers\jira\JiraSearchJQL;
use extas\components\quality\crawlers\jira\reactions\JiraReactionConfigItem;
use extas\components\quality\crawlers\jira\reactions\rates\JiraReactionRate;
use extas\components\quality\crawlers\jira\TJiraConfiguration;
use extas\interfaces\quality\crawlers\ICrawler;
use extas\interfaces\quality\crawlers\jira\IJiraIssue;
use extas\interfaces\quality\crawlers\jira\IJiraIssueChangelog;
use extas\interfaces\quality\crawlers\jira\IJiraSearchJQL;
use extas\interfaces\quality\crawlers\jira\reactions\IJiraReactionConfig as I;
use extas\interfaces\quality\crawlers\jira\reactions\IJiraReactionConfigItem;
use extas\interfaces\quality\crawlers\jira\reactions\rates\IJiraReactionRate;
use extas\interfaces\quality\crawlers\jira\reactions\rates\IJiraReactionRateRepository;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CrawlerJiraReaction
 *
 * @method jiraReactionRateRepository()
 *
 * @package extas\components\plugins\quality\crawlers
 * @author jeyroik@gmail.com
 */
class CrawlerJiraReaction extends Crawler
{
    use TJiraConfiguration;

    protected string $title = '[Jira] Reaction index';
    protected string $description = 'Calculate reaction index.';

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
                $this->inWorkTimes($inWorkTimes, $created, $inWork, $changeLog, $output);
                $this->resolvedTimes($resolvedTimes, $notResolved, $created, $resolved, $changeLog);
                $itemsCount++;
            }
            $rate = round($itemsCount / (
                1 + $this->median($inWorkTimes)/3600 + array_sum($resolvedTimes)/3600 + $notResolved
            ),4);

            $output->writeln([
                'Rate = ' . $itemsCount . ' / (1 + ' . ($this->median($inWorkTimes)/3600) . ' (median reaction time) + ' .
                (array_sum($resolvedTimes)/3600) . ' (total resolving time) + ' . $notResolved . ' (not resolved) )',
                'Rate =  <info>' . $rate . '</info>'
            ]);

            $this->saveRate($rate, $itemsCount, $output);
            return $this;

        } catch (\Exception $e) {
            $messages = explode('\n', $e->getMessage());
            $output->writeln($messages);
            return $this;
        }
    }

    /**
     * @param float $rate
     * @param int $issuesCount
     * @param OutputInterface $output
     */
    protected function saveRate($rate, int $issuesCount, OutputInterface &$output)
    {
        /**
         * @var $repo IJiraReactionRateRepository
         * @var $exist IJiraReactionRate
         */
        $month = (int) date('Ym');
        $repo = $this->jiraReactionRateRepository();
        $exist = $repo->one([IJiraReactionRate::FIELD__MONTH => $month]);

        if ($exist) {
            $exist->setRate($rate)->setTimestamp(time())->setCountTotal($issuesCount);
            $repo->update($exist);
            $output->writeln(['Rate updated']);
        } else {
            $repo->create(new JiraReactionRate([
                JiraReactionRate::FIELD__MONTH => $month,
                JiraReactionRate::FIELD__TIMESTAMP => time(),
                JiraReactionRate::FIELD__RATE => $rate,
                JiraReactionRate::FIELD__COUNT_TOTAL => $issuesCount
            ]));
            $output->writeln(['Rate created']);
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
        )->updatedDate(
            JiraSearchJQL::CONDITION__GREATER,
            JiraSearchJQL::DATE__START_OF_MONTH,
            -1
        );

        $config = $this->cfg();
        if (isset($config[I::FIELD__REACTION])) {
            $r = $config[I::FIELD__REACTION];
            if (isset($r[I::FIELD__PROJECTS]) && !empty($r[I::FIELD__PROJECTS])) {
                $jql->projectKey($r[I::FIELD__PROJECTS]);
            }
        }

        $jql->returnFields([IJiraIssue::FIELD__CREATED])
            ->expand([IJiraIssue::FIELD__CHANGELOG]);

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
     * @param OutputInterface $output
     */
    protected function inWorkTimes(
        array &$inWorkTimes,
        int $created,
        IJiraReactionConfigItem $inWork,
        IJiraIssueChangelog $changeLog,
        OutputInterface $output
    )
    {
        $item = $changeLog->one($inWork->getFrom(), $inWork->getTo());

        if ($item) {
            $output->writeln([
                '<info>Found "in work" changelog item</info>'
            ]);
            $inWorkTime = $item->getCreated(true);
            $inWorkTimes[] = $inWorkTime - $created;
        } else {
            $output->writeln([
                '<comment>Can not find "in work" changelog item</comment>'
            ]);
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
