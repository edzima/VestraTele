<?php

namespace backend\modules\settlement\models\search;

use common\models\issue\IssuePay;
use common\models\issue\query\IssuePayQuery;
use common\models\user\CustomerSearchInterface;
use Decimal\Decimal;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\QueryInterface;

/**
 * IssuePaySearch represents the model behind the search form of `common\models\issue\IssuePay`.
 */
class IssuePaySearch extends IssuePay implements CustomerSearchInterface {

	public $issue_id;

	public $customerLastname;
	public $calculationType;
	public $deadlineAtFrom;
	public $deadlineAtTo;

	public $delayRange = '- 7 days';

	protected const TABLE_ALIAS = 'basePay';

	public const PAY_STATUS_ALL = 'all';
	public const PAY_STATUS_ACTIVE = 'active';
	public const PAY_STATUS_DELAYED = 'delayed';
	public const PAY_STATUS_PAYED = 'payed';

	private string $payStatus;

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
			[['deadlineAtFrom', 'deadlineAtTo'], 'safe'],
			['customerLastname', 'string', 'min' => CustomerSearchInterface::MIN_LENGTH],
			[['value'], 'number'],
		];
	}

	public function setPayStatus(string $payStatus): void {
		$this->payStatus = $payStatus;
	}

	public function getPayStatus(): string {
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

	/**
	 * @inheritdoc
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
		$query = IssuePay::find();
		$query->alias(static::TABLE_ALIAS);
		$query->joinWith('calculation');
		$query->joinWith('calculation.issue.customer.userProfile CP');

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

		$this->applyCustomerSurnameFilter($query);
		$this->applyStatusFilter($query);

		// grid filtering conditions
		$query->andFilterWhere([
			'id' => $this->id,
			'basePay.issue.id' => $this->issue_id,
			'basePay.deadline_at' => $this->deadline_at,
			'basePay.transfer_type' => $this->transfer_type,
			'basePay.value' => $this->value,
			'calculation.type' => $this->calculationType,

		]);

		$query
			->andFilterWhere(['>=', 'deadline_at', $this->deadlineAtFrom])
			->andFilterWhere(['<=', 'deadline_at', $this->deadlineAtTo]);

		return $dataProvider;
	}

	protected function applyStatusFilter(QueryInterface $query): void {
		if (empty($this->status)) {
			$query->andWhere(['basePay.status' => null]);
		} else {
			$query->andWhere(['basePay.status' => $this->status]);
		}
	}

	public function getPayedSum(IssuePayQuery $query): Decimal {
		return $query->getPayedSum();
	}

	public function getNotPaySum(IssuePayQuery $query): Decimal {
		return $this->getValueSum($query) - $query->getPayedSum();
	}

	public function getValueSum(IssuePayQuery $query): Decimal {
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

	public function applyCustomerSurnameFilter(QueryInterface $query): void {
		if (!empty($this->customerLastname)) {
			$query->andWhere(['like', 'CP.lastname', $this->customerLastname . '%', false]);
		}
	}

}
