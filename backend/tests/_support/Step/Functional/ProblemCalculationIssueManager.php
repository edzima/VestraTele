<?php

namespace backend\tests\Step\Functional;

use common\models\user\User;

class ProblemCalculationIssueManager extends IssueManager {

	protected function getUsername(): string {
		return 'problem-calculation-issue-manager';
	}

	protected function getPermissions(): array {
		return array_merge(parent::getPermissions(), [User::PERMISSION_CALCULATION_PROBLEMS]);
	}
}
