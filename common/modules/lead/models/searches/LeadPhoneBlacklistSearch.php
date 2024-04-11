<?php

namespace common\modules\lead\models\searches;

use common\modules\lead\models\LeadPhoneBlacklist;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * LeadPhoneBlacklistSearch represents the model behind the search form of `common\modules\lead\models\LeadPhoneBlacklist`.
 */
class LeadPhoneBlacklistSearch extends LeadPhoneBlacklist {

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['phone', 'created_at'], 'safe'],
			[['user_id'], 'integer'],
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
		$query = LeadPhoneBlacklist::find();

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
			'created_at' => $this->created_at,
			'user_id' => $this->user_id,
		]);

		$query->andFilterWhere(['like', 'phone', $this->phone]);

		return $dataProvider;
	}
}
