<?php

namespace backend\modules\issue\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\issue\IssueTagType;

/**
 * TagTypeSearch represents the model behind the search form of `common\models\issue\IssueTagType`.
 */
class TagTypeSearch extends IssueTagType {

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id'], 'integer'],
			['issuesCount','default','value' => null],
			[['name', 'background', 'color', 'css_class', 'view_issue_position'], 'safe'],
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
	public function search(array $params = []) {
		$query = IssueTagType::find();

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
			->andFilterWhere(['like', 'background', $this->background])
			->andFilterWhere(['like', 'color', $this->color])
			->andFilterWhere(['like', 'css_class', $this->css_class])
            ->andFilterWhere(['like', 'view_issue_position', $this->view_issue_position]);

        return $dataProvider;
    }
}
