<?php

namespace common\models\issue;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * IssueMeetSearch represents the model behind the search form of `common\models\issue\IssueMeet`.
 */
class IssueMeetSearch extends IssueMeet {

	public $cityName;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'type_id', 'tele_id', 'agent_id', 'status', 'campaign_id'], 'integer'],
			[['phone', 'client_name', 'client_surname', 'created_at', 'updated_at', 'date_at', 'details', 'cityName'], 'safe'],
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
	public function search($params) {
		$query = IssueMeet::find();
		$query->with('city');
		$query->with('type');
		$query->with('campaign');
		$query->with(['agent.userProfile'])
			->with('tele.userProfile');

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
			'type_id' => $this->type_id,
			'tele_id' => $this->tele_id,
			'agent_id' => $this->agent_id,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'date_at' => $this->date_at,
			'status' => $this->status,
			'campaign_id' => $this->campaign_id,
		]);

		if (!empty($this->cityName)) {
			$query->joinWith('city C');
			$query->andFilterWhere(['like', 'c.name' => $this->cityName]);
		}

		$query->andFilterWhere(['like', 'phone', $this->phone])
			->andFilterWhere(['like', 'client_name', $this->client_name])
			->andFilterWhere(['like', 'client_surname', $this->client_surname])
			->andFilterWhere(['like', 'details', $this->details]);

		return $dataProvider;
	}
}
