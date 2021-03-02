<?php

namespace backend\modules\settlement\models\search;

use common\models\issue\IssueCost;
use common\models\issue\query\IssueCostQuery;
use common\models\user\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * IssueCostSearch represents the model behind the search form of `common\models\issue\IssueCost`.
 */
class IssueCostSearch extends IssueCost {

	public $withSettlements;

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['id', 'issue_id', 'user_id'], 'integer'],
			['type', 'string'],
			['withSettlements', 'boolean'],
			[['created_at', 'updated_at', 'date_at'], 'safe'],
			[['value', 'vat'], 'number'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios(): array {
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}

	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 */
	public function search(array $params): ActiveDataProvider {
		$query = IssueCost::find();
		$query->joinWith(['issue', 'settlements', 'user']);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'defaultOrder' => ['date_at' => SORT_ASC],
			],
		]);

		$this->load($params);
		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		$this->applySettlementsFilter($query);

		// grid filtering conditions
		$query->andFilterWhere([
			IssueCost::tableName() . '.id' => $this->id,
			IssueCost::tableName() . '.issue_id' => $this->issue_id,
			IssueCost::tableName() . '.user_id' => $this->user_id,
			IssueCost::tableName() . '.value' => $this->value,
			IssueCost::tableName() . '.vat' => $this->vat,
			IssueCost::tableName() . '.created_at' => $this->created_at,
			IssueCost::tableName() . '.date_at' => $this->date_at,
			IssueCost::tableName() . '.updated_at' => $this->updated_at,
			IssueCost::tableName() . '.type' => $this->type,
		]);

		return $dataProvider;
	}

	private function applySettlementsFilter(IssueCostQuery $query): void {
		if ($this->withSettlements === null || $this->withSettlements === '') {
			return;
		}
		if ($this->withSettlements) {
			$query->withSettlements();
			return;
		}
		$query->withoutSettlements();
	}

	public static function getUsersNames(): array {
		return User::getSelectList(IssueCost::find()
			->select('user_id')
			->distinct()
			->column()
			, false);
	}

}
