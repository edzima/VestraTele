<?php

namespace common\models\entityResponsible;

use common\models\issue\Issue;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * IssueEntityResponsibleSearch represents the model behind the search form of `common\models\issue\IssueEntityResponsible`.
 */
class EntityResponsibleSearch extends EntityResponsible {

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['id', 'is_for_summon'], 'integer'],
			[['name', 'details'], 'safe'],
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
	public function search(array $params) {
		$query = EntityResponsible::find();
		$query->select([
			EntityResponsible::tableName() . '.*',
			'COUNT(' . Issue::tableName() . '.id) AS issuesCount',
		])->joinWith('issues', false)
			->groupBy(EntityResponsible::tableName() . '.id');

		// add conditions that should always apply here
		$query->with('address');
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
		$dataProvider->getSort()->attributes['issuesCount'] = [
			'asc' => ['issuesCount' => SORT_ASC],
			'desc' => ['issuesCount' => SORT_DESC],
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
			'is_for_summon' => $this->is_for_summon,
		]);

		$query->andFilterWhere(['like', 'name', $this->name])
			->andFilterWhere(['like', 'details', $this->details]);

		return $dataProvider;
	}
}
