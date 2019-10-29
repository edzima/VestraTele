<?php

namespace backend\modules\issue\models\searches;

use common\models\issue\IssuePay;
use common\models\issue\IssuePayQuery;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * IssuePaySearch represents the model behind the search form of `common\models\issue\IssuePay`.
 */
class IssuePaySearch extends IssuePay {

	public $clientSurname;
	public $payCityState;
	public $deadlineAtFrom;
	public $deadlineAtTo;

	public $delayRange = '- 7 days';

	protected const TABLE_ALIAS = 'basePay';

	public const STATUS_ALL = 0;
	public const STATUS_ACTIVE = 10;
	public const STATUS_DELAYED = 20;
	public const STATUS_PAYED = 30;

	private $status;

	public function attributeLabels(): array {
		return array_merge(parent::attributeLabels(), [
			'deadlineAtFrom' => 'Termin od',
			'deadlineAtTo' => 'Termin do',
			'payCityState' => 'Region',

		]);
	}

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['id', 'issue_id', 'payCityState'], 'integer'],
			[['deadlineAtFrom', 'deadlineAtTo', 'clientSurname'], 'safe'],
			[['value'], 'number'],
		];
	}

	public function setStatus(int $status): void {
		$this->status = $status;
	}

	public function getStatus(): int {
		return $this->status;
	}

	public function isActive(): bool {
		return $this->status === static::STATUS_ACTIVE;
	}

	public function isDelayed(): bool {
		return $this->status === static::STATUS_DELAYED;
	}

	public function isPayed(): bool {
		return $this->status === static::STATUS_PAYED;
	}

	public static function getStatusNames(): array {
		return [
			static::STATUS_ACTIVE => 'Bieżące',
			static::STATUS_DELAYED => 'Przeterminowane',
			static::STATUS_PAYED => 'Opłacone',
			static::STATUS_ALL => 'Wszystkie',
		];
	}

	/**
	 * @inheritdoc
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
		$query = IssuePay::find();
		$query->alias(static::TABLE_ALIAS);
		$query->joinWith(['issue']);

		// add conditions that should always apply here
		$this->applyStatusFilter($query);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'defaultOrder' => ['deadline_at' => SORT_ASC],
			],
		]);

		$this->load($params);
		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		if (!empty($this->payCityState)) {
			$query->joinWith(['issue.payCity.city']);
			$query->andWhere(['miasta.wojewodztwo_id' => $this->payCityState]);
		}

		// grid filtering conditions
		$query->andFilterWhere([
			'id' => $this->id,
			'issue_id' => $this->issue_id,
			'basePay.deadline_at' => $this->deadline_at,
			'basePay.transfer_type' => $this->transfer_type,
			'basePay.type' => $this->type,
			'basePay.value' => $this->value,

		]);

		$query->andFilterWhere(['like', 'issue.client_surname', $this->clientSurname])
			->andFilterWhere(['>=', 'deadline_at', $this->deadlineAtFrom])
			->andFilterWhere(['<=', 'deadline_at', $this->deadlineAtTo]);

		return $dataProvider;
	}

	public function getPayedSum(IssuePayQuery $query): float {
		return $query->getPayedSum();
	}

	public function getNotPaySum(IssuePayQuery $query): float {
		return $this->getValueSum($query) - $query->getPayedSum();
	}

	public function getValueSum(IssuePayQuery $query): float {
		return $query->getValueSum();
	}

	private function applyStatusFilter(IssuePayQuery $query): void {
		switch ($this->status) {
			case static::STATUS_ALL:
				break;
			case static::STATUS_ACTIVE:
				$query->onlyNotPayed();
				$query->onlyNotDelayed($this->delayRange);
				break;
			case static::STATUS_DELAYED:
				$query->onlyDelayed($this->delayRange);
				$query->onlyWithoutDeadline();
				break;
			case static::STATUS_PAYED:
				$query->onlyPayed();
				break;
		}
	}
}
