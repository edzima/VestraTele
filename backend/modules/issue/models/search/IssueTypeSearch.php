<?php

namespace backend\modules\issue\models\search;

use common\models\issue\IssueType;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * IssueTypeSearch represents the model behind the search form of `common\models\issue\IssueType`.
 */
class IssueTypeSearch extends IssueType {

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['parent_id', 'lead_source_id'], 'integer'],
			[['with_additional_date', 'default_show_linked_notes', 'is_main'], 'boolean'],
			[['with_additional_date', 'default_show_linked_notes', 'is_main'], 'default', 'value' => null],
			[['name', 'short_name'], 'safe'],
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
		$query = IssueType::find();

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
			'with_additional_date' => $this->with_additional_date,
			'default_show_linked_notes' => $this->default_show_linked_notes,
			'parent_id' => $this->parent_id,
			'lead_source_id' => $this->lead_source_id,
			'is_main' => $this->is_main,
		]);

		$query->andFilterWhere(['like', 'name', $this->name])
			->andFilterWhere(['like', 'short_name', $this->short_name]);

		return $dataProvider;
	}
}
