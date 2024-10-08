<?php

namespace backend\modules\user\models\search;

use common\models\issue\IssueUser;
use common\models\user\query\UserQuery;
use common\models\user\Worker;
use yii\data\ActiveDataProvider;

class WorkersWithoutIssuesSearch extends WorkerUserSearch {

	public $role = [
		Worker::ROLE_AGENT,
		Worker::ROLE_CO_AGENT,
	];
	public $status = Worker::STATUS_ACTIVE;

	public array $excludesRoles = [
		Worker::ROLE_MANAGER,
		Worker::ROLE_BOOKKEEPER,
	];

	public static array $AVAILABLE_ROLES = Worker::ROLES;

	public function rules(): array {
		return array_merge(parent::rules(), [
			[['role'], 'required'],
			['role', 'in', 'range' => static::$AVAILABLE_ROLES, 'allowArray' => true],
			['status', 'in', 'range' => array_keys(WorkerUserSearch::getStatusesNames())],
			[['createdAtFrom', 'createdAtTo'], 'safe'],
		]);
	}

	public function search(array $params): ActiveDataProvider {
		$dataProvider = parent::search($params);
		$query = $dataProvider->query;

		$query->andWhere([
			'NOT IN',
			Worker::tableName() . '.id',
			IssueUser::find()->select(('user_id'))->distinct(),
		]);

		return $dataProvider;
	}

	protected function applyAssigmentFilter(UserQuery $query): void {
		$roles = array_diff($this->role, $this->excludesRoles);
		$query->onlyAssignments($roles, false);
	}

	public static function getRolesNames(): array {
		$rolesNames = Worker::getRolesNames();
		$names = [];
		foreach (static::$AVAILABLE_ROLES as $role) {
			$names[$role] = $rolesNames[$role];
		}
		return $names;
	}
}
