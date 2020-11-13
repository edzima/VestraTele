<?php

namespace backend\modules\issue\models\search;

use common\models\issue\IssueNote;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * IssueNoteSearch represents the model behind the search form of `common\models\issue\IssueNote`.
 */
class IssueNoteSearch extends IssueNote {

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['id', 'issue_id', 'user_id', 'created_at', 'updated_at'], 'integer'],
			[['title', 'description'], 'safe'],
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
		$query = IssueNote::find();

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
			'issue_id' => $this->issue_id,
			'user_id' => $this->user_id,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
		]);

		$query->andFilterWhere(['like', 'title', $this->title])
			->andFilterWhere(['like', 'description', $this->description]);

		return $dataProvider;
	}
}
