<?php

namespace backend\tests\Step\Functional;

use common\models\user\User;

/**
 * Class CreateCalculationIssueManager
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class CreateCalculationIssueManager extends IssueManager {

	protected function getUsername(): string {
		return 'create-calculation-issue-manager';
	}

	protected function getPermissions(): array {
		return array_merge(parent::getPermissions(), [User::PERMISSION_CALCULATION_TO_CREATE]);
	}
}
