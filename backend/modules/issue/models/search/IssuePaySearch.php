<?php

namespace backend\modules\issue\models\search;

use common\models\address\State;
use common\models\issue\IssuePay;
use common\models\issue\query\IssuePayQuery;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * IssuePaySearch represents the model behind the search form of `common\models\issue\IssuePay`.
 */
class IssuePaySearch extends IssuePay {

	public $issue_id;

	public $clientSurname;
	public $calculationType;
	public $deadlineAtFrom;
	public $deadlineAtTo;

	public $delayRange = '- 7 days';

	protected const TABLE_ALIAS = 'basePay';

	public const PAY_STATUS_ALL = 0;
	public const PAY_STATUS_ACTIVE = 10;
	public const PAY_STATUS_DELAYED = 20;
	public const PAY_STATUS_PAYED = 30;

	private $payStatus;

	public function attributeLabels(): array {
		return array_merge(parent::attributeLabels(), [
			'deadlineAtFrom' => 'Termin od',
			'deadlineAtTo' => 'Termin do',
		]);
	}

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['id', 'issue_id', 'status', 'calculationType'], 'integer'],
			[['deadlineAtFrom', 'deadlineAtTo', 'clientSurname'], 'safe'],
			[['value'], 'number'],
		];
	}

	public function setPayStatus(int $payStatus): void {
		$this->payStatus = $payStatus;
	}

	public function getPayStatus(): int {
		return $this->payStatus;
	}

	public function isActive(): bool {
		return $this->payStatus === static::PAY_STATUS_ACTIVE;
	}

	public function isDelayed(): bool {
		return $this->payStatus === static::PAY_STATUS_DELAYED;
	}

	public function isPayed(): bool {
		return $this->payStatus === static::PAY_STATUS_PAYED;
	}

	public static function getPayStatusNames(): array {
		return [
			static::PAY_STATUS_ACTIVE => 'Bieżące',
			static::PAY_STATUS_DELAYED => 'Przeterminowane',
			static::PAY_STATUS_PAYED => 'Opłacone',
			static::PAY_STATUS_ALL => 'Wszystkie',
		];
	}

	public static function getStateNames(): array {
		return State::getSelectList();
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
		$query->joinWith('calculation as calculation');
		$query->joinWith(['issue']);
		$query->joinWith(['issue.customer.userProfile']);

		// add conditions that should always apply here
		$this->applyPayStatusFilter($query);

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

		// grid filtering conditions
		$query->andFilterWhere([
			'id' => $this->id,
			'basePay.issue.id' => $this->issue_id,
			'basePay.deadline_at' => $this->deadline_at,
			'basePay.transfer_type' => $this->transfer_type,
			'basePay.value' => $this->value,
			'basePay.status' => $this->status,
			'calculation.type' => $this->calculationType,

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

	private function applyPayStatusFilter(IssuePayQuery $query): void {
		switch ($this->payStatus) {
			case static::PAY_STATUS_ALL:
				break;
			case static::PAY_STATUS_ACTIVE:
				$query->onlyNotPayed();
				$query->onlyNotDelayed($this->delayRange);
				break;
			case static::PAY_STATUS_DELAYED:
				$query->onlyDelayed($this->delayRange);
				$query->onlyWithoutDeadline();
				break;
			case static::PAY_STATUS_PAYED:
				$query->onlyPayed();
				break;
		}
	}
}
