<?php

namespace common\modules\lead\models\searches;

use common\modules\lead\models\LeadQuestion;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

/**
 * LeadReportSchemaSearch represents the model behind the search form of `common\modules\lead\models\LeadReportSchema`.
 */
class LeadQuestionSearch extends LeadQuestion {

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'type_id', 'status_id', 'show_in_grid', 'order'], 'integer'],
			[
				[
					'is_active',
					'is_required',
				], 'boolean',
			],
			[['is_active', 'is_required'], 'default', 'value' => null],
			[['name', 'placeholder', 'type'], 'safe'],
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
		$query = LeadQuestion::find();
		$query->joinWith('leadStatus S');
		$query->joinWith('leadType T');

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$dataProvider->getSort()->attributes['order']['asc'] = [new Expression('-`order` DESC')];

		$this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		// grid filtering conditions
		$query->andFilterWhere([
			LeadQuestion::tableName() . '.id' => $this->id,
			LeadQuestion::tableName() . '.status_id' => $this->status_id,
			LeadQuestion::tableName() . '.type_id' => $this->type_id,
			LeadQuestion::tableName() . '.is_active' => $this->is_active,
			LeadQuestion::tableName() . '.is_required' => $this->is_required,
			LeadQuestion::tableName() . '.show_in_grid' => $this->show_in_grid,
			LeadQuestion::tableName() . '.order' => $this->order,
			LeadQuestion::tableName() . '.type' => $this->type,
		]);

		$query->andFilterWhere(['like', LeadQuestion::tableName() . '.name', $this->name])
			->andFilterWhere(['like', LeadQuestion::tableName() . '.placeholder', $this->placeholder]);

		return $dataProvider;
	}
}
