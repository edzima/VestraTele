<?php

namespace frontend\models\search;

use common\components\rbac\SettlementTypeAccessManager;
use common\models\issue\IssueInterface;

class IssueViewPayCalculationSearch extends IssuePayCalculationSearch {

	public string $action = SettlementTypeAccessManager::ACTION_ISSUE_VIEW;
	public ?bool $is_percentage = null;
	public bool $onlyToPayed = false;
	public bool $withAgents = false;
	public bool $withArchive = true;
	public ?bool $onlyWithPayProblems = null;
	public $problem_status = null;

	public function __construct(IssueInterface $model, $userId, array $config = []) {
		$this->issue_id = $model->getIssueId();
		parent::__construct($userId, $config);
	}
}
