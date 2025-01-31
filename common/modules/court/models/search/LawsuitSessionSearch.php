<?php

namespace common\modules\court\models\search;

use common\modules\court\models\Court;
use common\modules\court\models\Lawsuit;
use common\modules\court\models\LawsuitSession;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * LawsuitSessionSearch represents the model behind the search form of `common\modules\court\models\LawsuitSession`.
 */
class LawsuitSessionSearch extends LawsuitSession {

	public string $lawsuitSignature = '';
	public string $lawsuitCourtName = '';

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'lawsuit_id', 'is_cancelled', 'presence_of_the_claimant'], 'integer'],
			[['details', 'date_at', 'created_at', 'updated_at', 'room', 'lawsuitSignature', 'lawsuitCourtName'], 'safe'],
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
		$query = LawsuitSession::find();
		$query->with([
			'lawsuit',
			'lawsuit.court',
		]);

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

		$this->applyLawsuitFilter($query);

		// grid filtering conditions
		$query->andFilterWhere([
			'id' => $this->id,
			'lawsuit_id' => $this->lawsuit_id,
			'date_at' => $this->date_at,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'is_cancelled' => $this->is_cancelled,
			'presence_of_the_claimant' => $this->presence_of_the_claimant,
		]);

		$query->andFilterWhere(['like', 'details', $this->details])
			->andFilterWhere(['like', 'room', $this->room]);

		return $dataProvider;
	}

	private function applyLawsuitFilter(ActiveQuery $query): void {
		if (!empty($this->lawsuitCourtName)) {
			$query->joinWith('lawsuit.court');
			$query->andWhere(['like', Court::tableName() . '.name', $this->lawsuitCourtName]);
		}
		if (!empty($this->lawsuitSignature)) {
			$query->joinWith('lawsuit');
			$query->andWhere(['like', Lawsuit::tableName() . '.signature_act', $this->lawsuitSignature]);
		}
	}
}
