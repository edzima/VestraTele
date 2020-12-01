<?php

namespace backend\tests\_support\Step\Functional;

use backend\tests\Step\Functional\IssueManager;
use common\models\user\User;

class ExportIssueManager extends IssueManager {

	protected function getPermissions(): array {
		return array_merge(parent::getPermissions(), [User::PERMISSION_EXPORT]);
	}
}
