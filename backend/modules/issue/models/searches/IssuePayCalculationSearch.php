<?php

namespace backend\modules\issue\models\searches;

use yii\base\Model;
use common\models\issue\Issue;
use yii\data\ActiveDataProvider;
use common\models\issue\IssuePayCalculation;

/**
 * IssuePayCalculationSearch represents the model behind the search form of `common\models\issue\IssuePayCalculation`.
 */
class IssuePayCalculationSearch extends IssuePayCalculation {

	public $id;

	public $clientSurname;
	public $cityName;

	private $onlyNew = false;

	public function isOnlyNew(): bool {
		return $this->onlyNew;
	}

	protected function setIsOnlyNew(bool $value): void {
		$this->onlyNew = $value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['issue_id', 'pay_type', 'id'], 'integer'],
			[['value'], 'number'],
			[['details', 'cityName', 'clientSurname'], 'safe'],
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
		$query = Issue::find();
		$query->joinWith('payCalculation as calculation');

		if ($this->isOnlyNew()) {
			$this->status = null;
			$query->onlyPositiveDecision();
			$query->andWhere('calculation.issue_id IS NULL');
		} else {
			$query->andFilterWhere(['calculation.status' => $this->status]);
		}

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'attributes' => [
					'value' => [
						'asc' => ['calculation.value' => SORT_ASC],
						'desc' => ['calculation.value' => SORT_DESC],
					],
					'created_at' => [
						'asc' => ['calculation.created_at' => SORT_ASC],
						'desc' => ['calculation.created_at' => SORT_DESC],
					],
					'updated_at' => [
						'asc' => ['calculation.updated_at' => SORT_ASC],
						'desc' => ['calculation.updated_at' => SORT_DESC],
					],
				],
				'defaultOrder' => 'calculation.updated_at DESC',
			],
		]);

		$this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		if (!empty($this->cityName)) {
			$query->joinWith('clientCity');
			$query->andFilterWhere(['like', 'miasta.name', $this->cityName]);
		}

		// grid filtering conditions
		$query->andFilterWhere([
			'calculation.value' => $this->value,
			'calculation.status' => $this->status,
			'calculation.pay_type' => $this->pay_type,
		]);

		$query->andFilterWhere([
			'like', 'calculation.details', $this->details,
		])
			->andFilterWhere(['like', 'client_surname', $this->clientSurname])
			->andFilterWhere(['like', 'id', $this->id]);

		return $dataProvider;
	}

}
