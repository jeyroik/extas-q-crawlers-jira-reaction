<?php
namespace extas\components\quality\crawlers\jira\reactions\rates;

use extas\components\repositories\Repository;
use extas\interfaces\quality\crawlers\jira\reactions\rates\IJiraReactionRateRepository;

/**
 * Class JiraReactionRateRepository
 *
 * @package extas\components\quality\crawlers\jira\reactions\rates
 * @author jeyroik@gmail.com
 */
class JiraReactionRateRepository extends Repository implements IJiraReactionRateRepository
{
    protected $itemClass = JiraReactionRate::class;
    protected $name = 'jira_reaction_rates';
    protected $pk = JiraReactionRate::FIELD__MONTH;
    protected $scope = 'extas';
    protected $idAs = '';
}
