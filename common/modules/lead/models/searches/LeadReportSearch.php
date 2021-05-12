<?php

namespace common\modules\lead\models\searches;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\lead\models\LeadReport;

/**
 * LeadReportSearch represents the model behind the search form of `common\modules\lead\models\LeadReport`.
 */
class LeadReportSearch extends LeadReport {

	public $lead_type_id;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'lead_id', 'owner_id', 'status_id', 'old_status_id', 'lead_type_id'], 'integer'],
			[['details', 'created_at', 'updated_at'], 'safe'],
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
		$query = LeadReport::find();

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
			'lead_id' => $this->lead_id,
			'owner_id' => $this->owner_id,
			'status_id' => $this->status_id,
			'old_status_id' => $this->old_status_id,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
		]);

		$query->andFilterWhere(['like', 'details', $this->details]);

		return $dataProvider;
	}
}
