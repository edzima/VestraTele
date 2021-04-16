<?php

namespace common\modules\lead\models\searches;

use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadType;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * LeadSourceSearch represents the model behind the search form of `common\modules\lead\models\LeadSource`.
 */
class LeadSourceSearch extends LeadSource {

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'sort_index', 'owner_id', 'type_id'], 'integer'],
			[['name', 'url', 'phone'], 'safe'],
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
		$query = LeadSource::find();

		$query->with(['leadType', 'owner']);

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
			'sort_index' => $this->sort_index,
			'owner_id' => $this->owner_id,
		]);

		$query->andFilterWhere(['like', 'name', $this->name])
			->andFilterWhere(['like', 'url', $this->url]);

		return $dataProvider;
	}

	public static function getTypesNames(): array {
		return LeadType::getNames();
	}
}
