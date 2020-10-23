<?php

namespace common\models\issue\query;

use common\models\issue\IssueUser;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Issue]].
 *
 * @see IssueQuery
 */
class IssueUserQuery extends ActiveQuery {

	public function withType(string $type): self {
		$this->andWhere(['type' => $type]);
		return $this;
	}

	public function withTypes(array $types): self {
		$this->andWhere(['type' => $types]);
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
