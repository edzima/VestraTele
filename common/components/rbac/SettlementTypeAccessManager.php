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

	protected array $appsActions = [
		self::APP_BACKEND => [
			self::ACTION_ISSUE_VIEW,
			self::ACTION_VIEW,
			self::ACTION_PAYS,
			self::ACTION_CREATE,
			self::ACTION_DELETE,
			self::ACTION_INDEX,
		],
		self::APP_FRONTEND => [
			self::ACTION_ISSUE_VIEW,
			self::ACTION_VIEW,
			self::ACTION_PAYS,
			self::ACTION_INDEX,
		],
	];

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
