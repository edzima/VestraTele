<?php

namespace frontend\tests\Step\acceptance;

use common\models\user\User;

class MeetIssueManager extends IssueManager {

	protected function getPermissions(): array {
		return array_merge(parent::getPermissions(), [User::PERMISSION_MEET]);
	}
	protected function getRoles(): array {
		return array_merge(parent::getRoles(),[User::ROLE_AGENT] );
	}
}
