<?php

namespace common\models\issue\query;

use common\models\issue\IssueUser;
use common\models\user\query\UserProfileQuery;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Issue]].
 *
 * @see IssueQuery
 */
class IssueUserQuery extends ActiveQuery {

	public function users(array $ids): self {
		$this->andWhere([IssueUser::tableName() . '.user_id' => $ids]);
		return $this;
	}

	public function onlyWorkers(): self {
		return $this->withTypes(IssueUser::TYPES_WORKERS);
	}

	public function withType(string $type): self {
		[, $alias] = $this->getTableNameAndAlias();

		$this->andWhere([$alias . '.type' => $type]);
		return $this;
	}

	public function withUserFullName(string $name): self {
		$this->joinWith([
			'user.userProfile' => function (UserProfileQuery $query) use ($name) {
				$query->withFullName($name);
			},
		]);
		return $this;
	}

	public function withTypes(array $types): self {
		[, $alias] = $this->getTableNameAndAlias();

		$this->andWhere([$alias . '.type' => $types]);
		return $this;
	}

	/**
	 * @inheritdoc
	 * @return IssueUser[]|array
	 */
	public function all($db = null) {
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return IssueUser|array|null
	 */
	public function one($db = null) {
		return parent::one($db);
	}

}
