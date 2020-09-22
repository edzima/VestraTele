<?php

namespace common\models\issue\search;

use common\models\issue\Summon;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SummonSearch represents the model behind the search form of `common\models\issue\Summon`.
 */
class SummonSearch extends Summon {

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'type', 'status', 'term', 'created_at', 'updated_at', 'realized_at', 'start_at', 'issue_id', 'owner_id', 'contractor_id'], 'integer'],
			[['title'], 'safe'],
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
	 * @return ActiveDataProvider
	 */
	public function search(array $params): ActiveDataProvider {
		$query = Summon::find();
		$query->with('issue');

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
		$dataProvider->sort->defaultOrder = [
			'start_at' => SORT_DESC,
		];

		$this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		// grid filtering conditions
		$query->andFilterWhere([
			'id' => $this->id,
			'type' => $this->type,
			'status' => $this->status,
			'term' => $this->term,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'start_at' => $this->start_at,
			'realized_at' => $this->realized_at,
			'owner_id' => $this->owner_id,
			'contractor_id' => $this->contractor_id,
		]);
		$query->andFilterWhere(['like', 'issue_id', $this->issue_id]);

		$query->andFilterWhere(['like', 'title', $this->title]);

		return $dataProvider;
	}
}
