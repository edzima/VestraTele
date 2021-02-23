<?php

namespace backend\modules\provision\models;

use common\models\provision\ProvisionType;
use common\models\provision\ProvisionUser;
use common\models\provision\ProvisionUserQuery;
use common\models\user\query\UserQuery;
use common\models\user\User;
use yii\base\BaseObject;

class ProvisionUserData extends BaseObject {

	public ?ProvisionType $type = null;

	private User $user;

	public function __construct(User $user, $config = []) {
		$this->user = $user;
		parent::__construct($config);
	}

	public function getUser(): User {
		return $this->user;
	}

	public function getTypeId(): ?int {
		return $this->type->id ?? null;
	}

	public function hasType(): bool {
		return $this->type !== null;
	}

	public function getTypesNotSet(): array {
		$typesIds = ProvisionUser::find()
			->forUser($this->user->id)
			->select('type_id')
			->distinct()
			->column();
		$types = ProvisionType::getTypes();
		if (empty($typesIds)) {
			return $types;
		}
		foreach ($typesIds as $id) {
			unset($types[$id]);
		}
		return $types;
	}

	public function getSelfQuery(): ProvisionUserQuery {
		return $this->applyTypeFilter(
			ProvisionUser::find()
				->onlySelf($this->user->id)
		);
	}

	public function getFromQuery(): ProvisionUserQuery {
		return $this->applyTypeFilter(
			ProvisionUser::find()
				->notSelf()
				->onlyTo($this->user->id)
		);
	}

	public function getToQuery(): ProvisionUserQuery {
		return $this->applyTypeFilter(
			ProvisionUser::find()
				->with('toUser.userProfile', 'type')
				->notSelf()
				->onlyFrom($this->user->id)
		);
	}

	public function getAllParentsQueryWithoutProvision(array $excludedUserIds = [], bool $typeHierarchyCheck = true): ?UserQuery {
		if ($typeHierarchyCheck && $this->hasType() && !$this->type->getWithHierarchy()) {
			return null;
		}
		if (!$this->getUser()->hasParent()) {
			return null;
		}
		if (empty($excludedUserIds)) {
			$excludedUserIds = $this->getToQuery()
				->select('to_user_id')
				->distinct()
				->column();
		}
		$query = $this->getUser()
			->getAllParentsQuery();
		if ($query && !empty($excludedUserIds)) {
			$query->andWhere(['NOT IN', 'id', $excludedUserIds]);
		}
		return $query;
	}

	public function getAllChildesQueryWithoutProvision(array $excludedUserIds = [], bool $typeHierarchyCheck = true): ?UserQuery {
		if ($typeHierarchyCheck && $this->hasType() && !$this->type->getWithHierarchy()) {
			return null;
		}
		if (empty($this->getUser()->getAllChildesIds())) {
			return null;
		}
		if (empty($excludedUserIds)) {
			$excludedUserIds = $this->getFromQuery()
				->select('from_user_id')
				->distinct()
				->column();
		}
		$query = $this->getUser()
			->getAllChildesQuery();
		if (!empty($excludedUserIds)) {
			$query->andWhere(['NOT IN', 'id', $excludedUserIds]);
		}
		return $query;
	}

	private function applyTypeFilter(ProvisionUserQuery $query): ProvisionUserQuery {
		if ($this->type) {
			$query->forType($this->type->id);
		}
		return $query;
	}
}
