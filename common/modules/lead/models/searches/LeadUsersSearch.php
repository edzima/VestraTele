<?php

namespace common\modules\lead\models\searches;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\lead\models\LeadUser;
use yii\data\DataProviderInterface;

/**
 * LeadUsersSearch represents the model behind the search form of `common\modules\lead\models\LeadUser`.
 */
class LeadUsersSearch extends LeadUser {

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['lead_id', 'user_id'], 'integer'],
			[['type'], 'safe'],
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
	public function search(array $params): DataProviderInterface {
		$query = LeadUser::find();
		$query->with('user');

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
			'lead_id' => $this->lead_id,
			'user_id' => $this->user_id,
		]);

		$query->andFilterWhere(['like', 'type', $this->type]);

		return $dataProvider;
	}
}
