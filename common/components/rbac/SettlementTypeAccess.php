<?php

namespace common\components\rbac;

use common\models\settlement\SettlementType;
use common\models\user\Worker;

class SettlementTypeAccess extends ModelAccess {

	public const ACTION_ISSUE_VIEW = 'issue-view';

	public string $modelClass = SettlementType::class;

	public function getActions(): array {
		return array_merge(parent::getActions(), [
			self::ACTION_ISSUE_VIEW,
		]);
	}

	public array $availableParentRoles = Worker::ROLES;
	public array $availableParentPermissions = [Worker::PERMISSION_ISSUE];

}
