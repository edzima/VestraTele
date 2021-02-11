<?php

namespace common\modules\lead\models\searches;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\lead\models\LeadReportSchema;

/**
 * LeadReportSchemaSearch represents the model behind the search form of `common\modules\lead\models\LeadReportSchema`.
 */
class LeadReportSchemaSearch extends LeadReportSchema {

	public $status_ids;
	public $types_ids;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id'], 'integer'],
			[['name', 'placeholder', 'status_ids', 'types_ids'], 'safe'],
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
		$query = LeadReportSchema::find();
		$query->joinWith('schemaStatusTypes ST');

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
			'ST.type_id' => $this->types_ids,
			'ST.status_id' => $this->status_ids,
		]);

		$query->andFilterWhere(['like', 'name', $this->name])
			->andFilterWhere(['like', 'placeholder', $this->placeholder]);

		return $dataProvider;
	}
}
