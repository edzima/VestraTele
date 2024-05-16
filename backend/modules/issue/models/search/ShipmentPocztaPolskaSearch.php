<?php

namespace backend\modules\issue\models\search;

use common\models\issue\IssueShipmentPocztaPolska;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ShipmentPocztaPolskaSearch represents the model behind the search form of `common\models\issue\IssueShipmentPocztaPolska`.
 */
class ShipmentPocztaPolskaSearch extends IssueShipmentPocztaPolska {

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['issue_id'], 'integer'],
			[['shipment_number', 'created_at', 'updated_at', 'shipment_at', 'finished_at', 'apiData'], 'safe'],
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
		$query = IssueShipmentPocztaPolska::find();

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
			'issue_id' => $this->issue_id,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'shipment_at' => $this->shipment_at,
			'finished_at' => $this->finished_at,
		]);

		$query->andFilterWhere(['like', 'shipment_number', $this->shipment_number])
			->andFilterWhere(['like', 'apiData', $this->apiData]);

		return $dataProvider;
	}
}
