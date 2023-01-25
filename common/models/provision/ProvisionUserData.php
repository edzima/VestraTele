<?php

namespace common\models\provision;

use common\models\user\query\UserQuery;
use common\models\user\User;
use yii\base\BaseObject;

class ProvisionUserData extends BaseObject {

	public ?ProvisionType $type = null;
	public ?string $date = null;

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
		$types = ProvisionType::getTypes(true);
		foreach ($types as $id => $type) {
			if ($type->getBaseType() !== null) {
				unset($types[$id]);
			}
		}
		if (empty($typesIds)) {
			return $types;
		}
		foreach ($typesIds as $id) {
			unset($types[$id]);
		}
		return $types;
	}

	public function hasSelfies(): bool {
		return $this->getSelfQuery()->exists();
	}

	public function getSelfQuery(): ProvisionUserQuery {
		return $this->applyFilter(
			ProvisionUser::find()
				->onlySelf($this->user->id)
		);
	}

	public function getFromQuery(): ProvisionUserQuery {
		return $this->applyFilter(
			ProvisionUser::find()
				->notSelf()
				->onlyTo($this->user->id)
		);
	}

	public function getToQuery(): ProvisionUserQuery {
		return $this->applyFilter(
			ProvisionUser::find()
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

	public function applyFilter(ProvisionUserQuery $query): ProvisionUserQuery {
		$this->applyDateFilter($query);
		$this->applyTypeFilter($query);
		return $query;
	}

	public function applyDateFilter(ProvisionUserQuery $query): void {
		if ($this->date) {
			$query->forDate($this->date);
		}
	}

	public function applyTypeFilter(ProvisionUserQuery $query): void {
		if ($this->type) {
			$query->forType($this->type->id);
		}
	}

}
