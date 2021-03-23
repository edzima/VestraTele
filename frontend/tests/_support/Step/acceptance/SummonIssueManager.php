<?php

namespace frontend\tests\Step\acceptance;

use common\models\user\User;

/**
 * Class SummonIssueManager
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class SummonIssueManager extends IssueManager {

	protected function getUsername(): string {
		return 'summon-issue-manager';
	}

	protected function getPermissions(): array {
		return array_merge(parent::getPermissions(), [User::PERMISSION_SUMMON]);
	}
}
