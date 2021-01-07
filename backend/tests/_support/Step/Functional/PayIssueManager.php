<?php

namespace backend\tests\Step\Functional;

use common\models\user\User;

class PayIssueManager extends IssueManager {

	protected function getUsername(): string {
		return 'pay-issue-manager';
	}

	protected function getPermissions(): array {
		return array_merge(parent::getPermissions(), [User::PERMISSION_PAY]);
	}
}
