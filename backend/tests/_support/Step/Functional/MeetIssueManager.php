<?php

namespace backend\tests\Step\Functional;

use common\models\user\User;

class MeetIssueManager extends IssueManager {

	protected function getPermissions(): array {
		return array_merge(parent::getPermissions(), [User::PERMISSION_MEET]);
	}
}
