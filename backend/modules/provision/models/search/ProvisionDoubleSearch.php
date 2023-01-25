<?php

namespace backend\modules\provision\models\search;

use common\models\issue\IssuePayCalculation;
use common\models\provision\ProvisionDouble;
use common\models\provision\ProvisionSearch;
use common\models\user\User;
use yii\data\ActiveDataProvider;

class ProvisionDoubleSearch extends ProvisionSearch {

	private static $usersNames = [];

	public static function getUsersNames(): array {
		if (empty(static::$usersNames)) {
			static::$usersNames = User::getSelectList(
				ProvisionDouble::find()
					->select('to_user_id')
					->distinct()
					->column(), false);
		}
		return static::$usersNames;
	}

	public function getToUsersList(bool $dateFilter = true): array {
		return static::getSettlementTypesNames();
	}

	public static function getSettlementTypesNames(): array {
		return IssuePayCalculation::getTypesNames();
	}

	public function search(array $params): ActiveDataProvider {
		$query = ProvisionDouble::find();

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if (!$this->validate()) {
			$query->andWhere('0=1');
			return $dataProvider;
		}

		$this->applyDateFilter($query);
		$this->applyPayStatusFilter($query);

		$query->andFilterWhere(['to_user_id' => $this->to_user_id]);
		$query->andFilterWhere(['from_user_id' => $this->from_user_id]);

		return $dataProvider;
	}
}
