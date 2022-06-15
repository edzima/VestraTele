<?php

namespace common\modules\lead\models\searches;

use common\modules\lead\models\LeadMarketUser;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * LeadMarketUserSearch represents the model behind the search form of `common\modules\lead\models\LeadMarketUser`.
 */
class LeadMarketUserSearch extends LeadMarketUser {

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'market_id', 'lead_id', 'status', 'user_id'], 'integer'],
			[['created_at', 'updated_at'], 'safe'],
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
	public function search(array $params) {
		$query = LeadMarketUser::find();

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
			'market_id' => $this->market_id,
			'lead_id' => $this->lead_id,
			'user_id' => $this->user_id,
			'status' => $this->status,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
		]);

		return $dataProvider;
	}
}
