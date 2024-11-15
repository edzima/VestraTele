<?php

namespace frontend\models\search;

use common\components\rbac\SettlementTypeAccessManager;
use common\models\issue\query\IssuePayCalculationQuery;
use common\models\settlement\search\IssuePayCalculationSearch as BaseIssuePayCalculationSearch;
use common\models\user\User;
use Yii;

class IssuePayCalculationSearch extends BaseIssuePayCalculationSearch {

	public string $action = SettlementTypeAccessManager::ACTION_INDEX;
	public const PROBLEM_STATUS_NONE = -1;

	public $problem_status = self::PROBLEM_STATUS_NONE;
	public bool $onlyToPayed = true;

	public ?bool $is_percentage = false;

	public function __construct($userId, $config = []) {
		parent::__construct($config);
		$this->userId = $userId;
	}

	public function getAgentsNames(): array {
		if (count($this->issueUsersIds) > 1) {
			return User::getSelectList($this->issueUsersIds);
		}
		return [];
	}

	protected function applyProblemStatusFilter(IssuePayCalculationQuery $query): void {
		if ((int) $this->problem_status === static::PROBLEM_STATUS_NONE) {
			$this->onlyWithPayProblems = false;
		}
		parent::applyProblemStatusFilter($query);
	}

	public static function getProblemStatusesNames(): array {
		$names = parent::getProblemStatusesNames();
		$names[static::PROBLEM_STATUS_NONE] = Yii::t('settlement', 'Without problems');
		return $names;
	}

}
