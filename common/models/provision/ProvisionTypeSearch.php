<?php

namespace common\models\provision;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ProvisionTypeSearch represents the model behind the search form of `common\models\provision\ProvisionType`.
 */
class ProvisionTypeSearch extends ProvisionType {

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['only_with_tele', 'is_default', 'is_percentage'], 'boolean'],
			[['id'], 'integer'],
			[['name', 'value', 'date_from', 'date_to'], 'safe'],
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
		$query = ProvisionType::find();

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
			'date_from' => $this->date_from,
			'date_to' => $this->date_to,
			'value' => $this->value,
			'only_with_tele' => $this->only_with_tele,
			'is_default' => $this->is_default,
			'is_percentage' => $this->is_percentage,
		]);

		$query->andFilterWhere(['like', 'name', $this->name]);

		return $dataProvider;
	}

}
