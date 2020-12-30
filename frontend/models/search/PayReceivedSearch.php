<?php

namespace frontend\models\search;

use common\models\settlement\PayReceived;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;

class PayReceivedSearch extends PayReceived {

	public function rules(): array {
		return [];
	}

	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 */
	public function search(array $params): ActiveDataProvider {
		if (empty($this->user_id)) {
			throw new InvalidConfigException('$user_id must be set.');
		}
		$query = PayReceived::find()
			->andWhere(['user_id' => $this->user_id])
			->andWhere(['transfer_at' => null]);
		
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

		return $dataProvider;
	}
}
