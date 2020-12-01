<?php

namespace backend\tests\Step\Functional;

use common\models\user\User;

class IssueManager extends Manager {

	protected function getPermissions(): array {
		return [
			User::PERMISSION_ISSUE,
		];
	}

	public function assignNotePermission(): void {
		$this->assignPermission(User::PERMISSION_NOTE);
	}

	public function assignArchivePermission(): void {
		$this->assignPermission(User::PERMISSION_ARCHIVE);
	}

}
