<?php

namespace common\modules\reminder\models\searches;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\reminder\models\Reminder;

/**
 * ReminderSearch represents the model behind the search form of `common\modules\reminder\models\Reminder`.
 */
class ReminderSearch extends Reminder {

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'priority', 'created_at', 'updated_at'], 'integer'],
			[['date_at', 'details'], 'safe'],
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
	public function search(array $params) {
		$query = Reminder::find();

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
			'priority' => $this->priority,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'date_at' => $this->date_at,
		]);

		$query->andFilterWhere(['like', 'details', $this->details]);

		return $dataProvider;
	}
}
