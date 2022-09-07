<?php

namespace common\models\hint\searches;

use common\models\user\User;
use edzima\teryt\models\query\SimcQuery;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\hint\HintCity;
use yii\data\DataProviderInterface;
use yii\db\ActiveQuery;

/**
 * HintCitySearch represents the model behind the search form of `common\models\hint\HintCity`.
 */
class HintCitySearch extends HintCity {

	public static function getUsersNames(): array {
		return User::getSelectList(HintCity::find()->select('user_id')->column());
	}

	public string $cityName = '';

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'user_id', 'city_id'], 'integer'],
			[['type', 'status', 'details', 'cityName'], 'safe'],
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
	public function search(array $params): DataProviderInterface {
		$query = HintCity::find();
		$query->with('user.userProfile');
		$query->with('city');

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
			'user_id' => $this->user_id,
			'city_id' => $this->city_id,
			'type' => $this->type,
			'status' => $this->status,
		]);

		$query->andFilterWhere(['like', 'details', $this->details]);

		$this->applyCityFilter($query);

		return $dataProvider;
	}

	private function applyCityFilter(ActiveQuery $query): void {
		if (!empty($this->cityName)) {
			$query->joinWith([
				'city' => function (SimcQuery $query) {
					$query->startWithName($this->cityName);
				},
			]);
		}
	}
}
