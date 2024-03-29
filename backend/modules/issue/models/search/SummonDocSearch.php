<?php

namespace backend\modules\issue\models\search;

use common\models\issue\SummonDoc;
use common\models\issue\SummonType;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SummonDocSearch represents the model behind the search form of `common\models\issue\SummonDoc`.
 */
class SummonDocSearch extends SummonDoc {

	public $summonTypesIds;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'priority'], 'integer'],
			['summonTypesIds', 'in', 'range' => array_keys(SummonType::getNames())],
			[['name'], 'safe'],
		];
	}

	/**
	 * {@inheritdoc}
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
		$query = SummonDoc::find();

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
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
			'priority' => $this->priority,
		]);

		$query->andFilterWhere(['like', 'name', $this->name]);
		if (!empty($this->summonTypesIds)) {
			$query->summonTypes((array) $this->summonTypesIds);
		}

		return $dataProvider;
	}
}
