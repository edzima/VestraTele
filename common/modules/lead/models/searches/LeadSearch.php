<?php

namespace common\modules\lead\models\searches;

use common\models\SearchModel;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\LeadType;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\lead\models\Lead;

/**
 * LeadSearch represents the model behind the search form of `common\modules\lead\models\Lead`.
 */
class LeadSearch extends Lead implements SearchModel {

	public $type_id;
	public $firstname;
	public $lastname;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'status_id', 'type_id', 'source_id'], 'integer'],
			[['date_at', 'data', 'phone', 'email', 'postal_code', 'firstname', 'lastname'], 'safe'],
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
	public function search(array $params): ActiveDataProvider {
		$query = Lead::find()
			->joinWith('leadSource S')
			->with('reports');

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
			'date_at' => $this->date_at,
			'status_id' => $this->status_id,
			'source_id' => $this->source_id,
			'S.type_id' => $this->type_id,
		]);

		$query
			->andFilterWhere(['like', 'data', $this->data])
			->andFilterWhere(['like', 'phone', $this->phone])
			->andFilterWhere(['like', 'email', $this->email])
			->andFilterWhere(['like', 'postal_code', $this->postal_code]);

		return $dataProvider;
	}

	public static function getStatusNames(): array {
		return LeadStatus::getNames();
	}

	public static function getTypesNames(): array {
		return LeadType::getNames();
	}

	public static function getSourcesNames(): array {
		return LeadSource::getNames();
	}
}
