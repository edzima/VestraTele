<?php

namespace backend\modules\issue\models\search;

use common\models\issue\IssueTag;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * TagSearch represents the model behind the search form of `common\models\issue\IssueTag`.
 */
class TagSearch extends IssueTag {

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'is_active'], 'integer'],
			[['name', 'description', 'type'], 'safe'],
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
		$query = IssueTag::find();

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
			'is_active' => $this->is_active,
		]);

		$query->andFilterWhere(['like', 'name', $this->name])
			->andFilterWhere(['like', 'type', $this->type])
			->andFilterWhere(['like', 'description', $this->description]);

		return $dataProvider;
	}
}
