<?php

namespace common\modules\court\models\query;

use common\models\issue\IssueUser;
use common\modules\court\models\Court;
use common\modules\court\models\Lawsuit;
use yii\db\ActiveQuery;

class LawsuitQuery extends ActiveQuery {

	public function signature(string $signature): self {
		$this->andWhere(['signature_act' => $signature]);
		return $this;
	}

	public function court($court): self {
		if (is_int($court)) {
			$this->andWhere(['court_id' => $court]);
		} elseif (is_string($court)) {
			$this->joinWith('court');
			$this->andWhere([Court::tableName() . '.name' => $court]);
		} elseif (is_array($court)) {
			$this->andWhere(['court_id' => $court]);
		} elseif ($court instanceof Court) {
			$this->andWhere(['court_id' => $court->id]);
		}
		return $this;
	}

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
