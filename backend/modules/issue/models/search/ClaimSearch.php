<?php

namespace backend\modules\issue\models\search;

use common\helpers\ArrayHelper;
use common\models\issue\IssueClaim;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ProvisionSearch represents the model behind the search form of `common\models\issue\IssueProvision`.
 */
class ClaimSearch extends IssueClaim {

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'issue_id', 'is_percent', 'entity_responsible_id'], 'integer'],
			[['type', 'details'], 'safe'],
			[['trying_value', 'obtained_value'], 'number'],
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
		$query = IssueClaim::find();

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
			'type' => $this->type,
			'entity_responsible_id' => $this->entity_responsible_id,
			'issue_id' => $this->issue_id,
			'trying_value' => $this->trying_value,
			'obtained_value' => $this->obtained_value,
			'is_percent' => $this->is_percent,
		]);

		$query
			->andFilterWhere(['like', 'details', $this->details]);

		return $dataProvider;
	}

	public static function getEntityResponsibleNames(): array {
		return ArrayHelper::map(
			IssueClaim::find()
				->select('entity_responsible_id')
				->joinWith('entityResponsible')
				->distinct()
				->all(), 'entity_responsible_id', 'entityResponsible.name'

		);
	}
}
