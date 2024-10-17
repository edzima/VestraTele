<?php

namespace common\components\rbac;

use common\models\settlement\SettlementType;
use common\models\user\Worker;

class SettlementTypeAccessManager extends ModelAccessManager {

	public const ACTION_ISSUE_VIEW = 'issue-view';
	public const ACTION_PAYS = 'pays';

	public string $action = self::ACTION_ISSUE_VIEW;

	public array $availableParentRoles = Worker::ROLES;
	public ?string $managerPermission = Worker::PERMISSION_SETTLEMENT_TYPE_MANAGER;
	public array $availableParentPermissions = [
		Worker::PERMISSION_ISSUE,
		Worker::PERMISSION_COST,
	];

	public function getActions(): array {
		return array_merge(parent::getActions(), [
			self::ACTION_ISSUE_VIEW,
			self::ACTION_PAYS,
		]);
	}

	public function getIds(string|int $userId = null): array {
		if ($userId
			&& $this->managerPermission
			&& $this->auth->checkAccess($userId, $this->managerPermission)
		) {
			return array_keys(SettlementType::getModels());
		}
		return parent::getIds($userId);
	}
}
