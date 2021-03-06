<?php

namespace frontend\models\search;

use common\models\issue\IssueUser;
use common\models\issue\query\IssueQuery;
use common\models\issue\search\UserSearch;
use yii\data\ActiveDataProvider;

class IssueUserSearch extends UserSearch {

	public function rules(): array {
		return [
			['surname', 'required'],
			['type', 'in', 'range' => array_keys(static::getTypesNames())],
		];
	}

	public function search(array $params): ActiveDataProvider {
		$provider = parent::search($params);
		/** @var IssueQuery $query */
		$query = $provider->query;
		if (empty($this->surname)) {
			$this->clearErrors('surname');
			$provider->query->andWhere('1=0');
			return $provider;
		}
		if ($this->user_id > 0) {
			$query->andWhere(['issue_id' => IssueUser::find()->select('issue_id')->andWhere(['user_id' => $this->user_id])]);
		}
		$query->andWhere(['type' => array_keys(static::getTypesNames())]);

		return $provider;
	}

	public static function getTypesNames(): array {
		$names = parent::getTypesNames();
		foreach (static::TYPES_WORKERS as $type) {
			unset($names[$type]);
		}
		return $names;
	}

}
