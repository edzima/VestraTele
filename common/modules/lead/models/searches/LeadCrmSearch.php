<?php

namespace common\modules\lead\models\searches;

use common\modules\lead\models\LeadCrm;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * LeadCrmSearch represents the model behind the search form of `common\modules\lead\models\LeadCrm`.
 */
class LeadCrmSearch extends LeadCrm {

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id'], 'integer'],
			[['name', 'backend_url', 'frontend_url'], 'safe'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function scenarios() {
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
	public function search($params) {
		$query = LeadCrm::find();

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
		]);

		$query->andFilterWhere(['like', 'name', $this->name])
			->andFilterWhere(['like', 'backend_url', $this->backend_url])
			->andFilterWhere(['like', 'frontend_url', $this->frontend_url]);

		return $dataProvider;
	}
}
