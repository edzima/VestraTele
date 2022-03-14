<?php

namespace common\modules\lead\models\searches;

use common\models\user\User;
use common\modules\lead\models\LeadDialerType;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * LeadDialerTypeSearch represents the model behind the search form of `common\modules\lead\models\LeadDialerType`.
 */
class LeadDialerTypeSearch extends LeadDialerType {

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'status', 'user_id', 'type', 'did'], 'integer'],
			[['name'], 'safe'],
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
		$query = LeadDialerType::find();

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
			'status' => $this->status,
			'type' => $this->type,
			'user_id' => $this->user_id,
		]);

		$query->andFilterWhere(['like', 'name', $this->name]);
		$query->andFilterWhere(['like', 'did', $this->did]);

		return $dataProvider;
	}

	public static function getUsersNames(): array {
		return User::getSelectList(LeadDialerType::find()
			->select('user_id')
			->distinct()
			->column(),
			false
		);
	}
}
