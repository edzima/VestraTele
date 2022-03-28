<?php

namespace backend\modules\user\models\search;

use common\models\user\UserTrait;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserTraitSearch represents the model behind the search form of `common\models\user\UserTrait`.
 */
class UserTraitSearch extends UserTrait {

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'show_on_issue_view'], 'integer'],
			[['name'], 'safe'],
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
	public function search(array $params = []): ActiveDataProvider {
		$query = UserTrait::find();

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
			'show_on_issue_view' => $this->show_on_issue_view,
		]);

		$query->andFilterWhere(['like', 'name', $this->name]);

		return $dataProvider;
	}
}
