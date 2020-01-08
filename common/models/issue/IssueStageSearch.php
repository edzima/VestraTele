<?php

namespace common\models\issue;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * IssueStageSearch represents the model behind the search form of `common\models\issue\IssueStage`.
 */
class IssueStageSearch extends IssueStage {

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['id', 'days_reminder'], 'integer'],
			[['name', 'short_name'], 'safe'],
		];
	}

	/**
	 * @inheritdoc
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
		$query = IssueStage::find();

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
			'days_reminder' => $this->days_reminder,
		]);

		$query->andFilterWhere(['like', 'name', $this->name])
			->andFilterWhere(['like', 'short_name', $this->short_name]);

		$query->addOrderBy('posi');

		return $dataProvider;
	}
}
