<?php

namespace common\models\provision;

use common\models\user\Worker;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for ProvisionUser::class
 *
 * @see ProvisionUser
 */
class ProvisionUserQuery extends ActiveQuery {

	public function forDate(string $date): self {
		$this->andWhere([
			'and', [
				'or', ['<=', 'from_at', $date], ['from_at' => null],
			],
			[
				'or', ['>=', 'to_at', $date], ['to_at' => null],
			],
		]);
		return $this;
	}

	public function onlyTo(int $user_id): self {
		$this->andWhere(['to_user_id' => $user_id]);
		return $this;
	}

	public function onlyFrom(int $user_id): self {
		$this->andWhere(['from_user_id' => $user_id]);
		return $this;
	}

	public function forType(int $typeId): self {
		$this->andWhere(['type_id' => $typeId]);
		return $this;
	}

	public function forTypes(array $types): self {
		$this->andWhere(['type_id' => $types]);
		return $this;
	}

	public function forUser(int $userId): self {
		$this->andWhere(['or', ['from_user_id' => $userId, 'to_user_id' => $userId]]);
		return $this;
	}

	public function user(Worker $user): self {
		return $this->andWhere([
			'or', [
				'from_user_id' => $user->id,
				'to_user_id' => $user->getParentsIds(),
			],
			[
				'from_user_id' => $user->getAllChildesIds(),
				'to_user_id' => $user->id,
			],
			[
				'from_user_id' => $user->id,
				'to_user_id' => $user->id,
			],

		]);
	}

	public function onlyOverwritten(): self {
		[$table, $alias] = $this->getTableNameAndAlias();
		$this->joinWith('type T');
		$this->andWhere($alias . '.value != T.value');
		return $this;
	}

	public function onlyNotOverwritten(): self {
		[$table, $alias] = $this->getTableNameAndAlias();
		$this->joinWith('type T');
		$this->andWhere($alias . '.value = T.value');
		return $this;
	}

	public function onlySelf(int $user_id = null): self {
		if ($user_id === null) {
			$this->andWhere('from_user_id = to_user_id');
		} else {
			$this->andWhere([
				'from_user_id' => $user_id,
				'to_user_id' => $user_id,
			]);
		}

		return $this;
	}

	public function notSelf(): self {
		$this->andWhere('from_user_id <> to_user_id');
		return $this;
	}

	/**
	 * {@inheritdoc}
	 * @return ProvisionUser[]|array
	 */
	public function all($db = null) {
		return parent::all($db);
	}

	/**
	 * {@inheritdoc}
	 * @return ProvisionUser|array|null
	 */
	public function one($db = null) {
		return parent::one($db);
	}

}
