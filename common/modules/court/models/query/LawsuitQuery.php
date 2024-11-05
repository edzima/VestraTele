<?php

namespace common\modules\court\models\query;

use common\models\issue\IssueUser;
use common\modules\court\models\Lawsuit;
use yii\db\ActiveQuery;

class LawsuitQuery extends ActiveQuery {

	public function usersIssues(array $usersIds): self {
		$this->joinWith(['issues.users']);
		$this->andWhere([IssueUser::tableName() . '.user_id' => $usersIds]);
		return $this;
	}

	/**
	 * @param $db
	 * @return array|Lawsuit[]
	 */
	public function all($db = null) {
		return parent::all($db);
	}

	/**
	 * @param $db
	 * @return array|Lawsuit|null
	 */
	public function one($db = null) {
		return parent::one($db);
	}
}
