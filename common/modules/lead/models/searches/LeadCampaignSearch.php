<?php

namespace common\modules\lead\models\searches;

use common\modules\lead\models\LeadCampaign;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * LeadCampaignSearch represents the model behind the search form of `common\modules\lead\models\LeadCampaign`.
 */
class LeadCampaignSearch extends LeadCampaign {

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'sort_index', 'owner_id'], 'integer'],
			['!owner_id', 'required', 'on' => static::SCENARIO_OWNER],
			[['name', 'url_search_part'], 'safe'],
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
	public function search(array $params): ActiveDataProvider {
		$query = LeadCampaign::find();

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if (!$this->validate()) {
			$query->where('0=1');
			return $dataProvider;
		}

		// grid filtering conditions
		$query->andFilterWhere([
			'id' => $this->id,
			'sort_index' => $this->sort_index,
			'owner_id' => $this->owner_id,
		]);

		$query->andFilterWhere(['like', 'name', $this->name])
			->andFilterWhere(['like', 'url_search_part', $this->url_search_part]);

		return $dataProvider;
	}
}
