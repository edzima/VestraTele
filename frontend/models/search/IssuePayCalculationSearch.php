<?php

namespace frontend\models\search;

use common\models\settlement\search\IssuePayCalculationSearch as BaseIssuePayCalculationSearch;
use common\models\user\User;

class IssuePayCalculationSearch extends BaseIssuePayCalculationSearch {

	public function getAgentsNames(): array {
		if (count($this->issueUsersIds) > 1) {
			return User::getSelectList($this->issueUsersIds);
		}
		return [];
	}
}
