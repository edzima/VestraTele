<?php

namespace backend\modules\issue\models\search;

use common\models\issue\IssueCost;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * IssueCostSearch represents the model behind the search form of `common\models\issue\IssueCost`.
 */
class IssueCostSearch extends IssueCost {

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['id', 'issue_id'], 'integer'],
			['type', 'string'],
			[['created_at', 'updated_at', 'date_at'], 'safe'],
			[['value', 'vat'], 'number'],
		];
	}

	public static function getTypesNames(): array {
		return IssueCost::getTypesNames();
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios():array {
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
		$query->joinWith(['issue']);

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

		// grid filtering conditions
		$query->andFilterWhere([
			'id' => $this->id,
			'issue.id' => $this->issue_id,
			'value' => $this->value,
			'vat' => $this->vat,
			'created_at' => $this->created_at,
			'date_at' => $this->date_at,
			'updated_at' => $this->updated_at,
			'type' => $this->type,
		]);

		return $dataProvider;
	}

}
