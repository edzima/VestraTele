<?php

namespace common\modules\lead\models\searches;

use common\modules\lead\models\entities\LeadMarketOptions;
use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\LeadMarketUser;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * LeadMarketSearch represents the model behind the search form of `common\modules\lead\models\LeadMarket`.
 */
class LeadMarketSearch extends LeadMarket {

	public $booleanOptions;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'lead_id', 'status', 'creator_id', 'usersCount'], 'integer'],
			[['created_at', 'updated_at', 'options', 'booleanOptions', 'details'], 'safe'],
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
	public function search($params) {
		$query = LeadMarket::find();
		$query->select([
			LeadMarket::tableName() . '.*',
			'COUNT(' . LeadMarketUser::tableName() . '.market_id' . ') as usersCount',
		]);
		$query->groupBy(LeadMarket::tableName() . '.id');
		$query->joinWith('leadMarketUsers');

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
			'creator_id' => $this->creator_id,
			'status' => $this->status,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
		]);

		$query->andFilterWhere(['like', 'details', $this->details]);

		return $dataProvider;
	}

	public function getBooleanOptionsNames(): array {
		return LeadMarketOptions::getBooleanLabels();
	}
}
