<?php

namespace common\models\provision;

use common\models\issue\IssuePayCalculation;
use common\models\issue\query\IssuePayCalculationQuery;
use common\models\issue\query\IssuePayQuery;
use common\models\SearchModel;
use common\models\user\CustomerSearchInterface;
use common\models\user\query\UserQuery;
use common\models\user\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\QueryInterface;

/**
 * ProvisionSearch represents the model behind the search form of `common\models\provision\Provision`.
 */
class ProvisionSearch extends Provision implements CustomerSearchInterface, SearchModel {

	public const PAY_STATUS_PAID = 'paid';
	public const PAY_STATUS_UNPAID = 'unpaid';
	protected const DEFAULT_PAY_STATUS = self::PAY_STATUS_PAID;

	public $issue_id;

	public bool $defaultCurrentMonth = true;
	public $dateFrom;
	public $dateTo;
	public $customerLastname;

	public $settlementTypes = [];
	public $payStatus;

	public function isUnpaid(): bool {
		return $this->payStatus === static::PAY_STATUS_UNPAID;
	}

	public ?bool $withoutEmpty = null;
	public array $excludedFromUsers = [];

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['hide_on_report'], 'boolean'],
			[['pay_id', 'from_user_id', 'to_user_id', 'issue_id'], 'integer'],
			['type_id', 'in', 'range' => array_keys(static::getTypesNames()), 'allowArray' => true],
			['settlementTypes', 'in', 'range' => array_keys(static::getSettlementTypesNames()), 'allowArray' => true],
			['payStatus', 'in', 'range' => array_keys(static::getPayStatusNames())],
			['payStatus', 'default', 'value' => static::DEFAULT_PAY_STATUS],
			[['dateFrom', 'dateTo'], 'safe'],
			['customerLastname', 'string', 'min' => CustomerSearchInterface::MIN_LENGTH],
		];
	}

	public function attributeLabels(): array {
		return array_merge([
			'dateFrom' => Yii::t('provision', 'From at'),
			'dateTo' => Yii::t('provision', 'To at'),
			'payStatus' => Yii::t('settlement', 'Pay Status'),
			'settlementTypes' => Yii::t('settlement', 'Settlement type'),
			'excludedFromUsers' => Yii::t('provision','Excluded from Users'),
			'withoutEmpty' => Yii::t('provision','Without Empty'),
		], parent::attributeLabels());
	}

	public function init() {
		parent::init();
		if ($this->defaultCurrentMonth) {
			if (empty($this->dateFrom)) {
				$this->dateFrom = date('Y-m-d', strtotime('first day of this month'));
			}
			if (empty($this->dateTo)) {
				$this->dateTo = date('Y-m-d', strtotime('last day of this month'));
			}
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function scenarios() {
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}

	public function search(array $params): ActiveDataProvider {
		$query = Provision::find();
		$query
			->with('pay.issue')
			->with('pay.calculation')
			->with('pay.calculation.costs')
			->with('fromUser.userProfile')
			->with('toUser.userProfile')
			->with('type');

		$query->joinWith([
			'pay.issue.customer C' => function (UserQuery $query) {
				$query->joinWith('userProfile CP');
			},
		]);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if (!$this->validate()) {
			$query->andWhere('0=1');
			return $dataProvider;
		}

		$this->applyCustomerNameFilter($query);
		$this->applyDateFilter($query);
		$this->applyPayStatusFilter($query);
		$this->applySettlementFilter($query);
		$this->applyWithoutEmptyFilter($query);
		$this->applyExcluedFromUsersFilter($query);

		if (!empty($this->issue_id)) {
			$query->joinWith('pay.issue');
			$query->andWhere(['issue.id' => $this->issue_id]);
		}

		if ($this->hide_on_report) {
			$query->hidden();
		}

		// grid filtering conditions
		$query->andFilterWhere([
			Provision::tableName() . '.pay_id' => $this->pay_id,
			Provision::tableName() . '.to_user_id' => $this->to_user_id,
			Provision::tableName() . '.from_user_id' => $this->from_user_id,
			Provision::tableName() . '.type_id' => $this->type_id,
		]);

		return $dataProvider;
	}

	protected function applyWithoutEmptyFilter(ProvisionQuery $query): void {
		if ($this->withoutEmpty) {
			$query->andWhere([
				'>', Provision::tableName() . '.value', 0,
			]);
		}
	}

	protected function applyPayStatusFilter(ProvisionQuery $query): void {
		if (!empty($this->payStatus)) {
			$query->joinWith([
				'pay' => function (IssuePayQuery $query): void {
					switch ($this->payStatus) {
						case static::PAY_STATUS_UNPAID:
							$query->onlyUnpaid();
							break;
						case static::PAY_STATUS_PAID:
							$query->onlyPaid();
							break;
					}
				},
			]);
		}
	}

	public function getFromUserList(bool $dateFilter = true): array {
		$query = Provision::find()
			->select('from_user_id')
			->andFilterWhere(['to_user_id' => $this->to_user_id])
			->distinct();

		if ($dateFilter) {
			$this->applyDateFilter($query);
		}
		$this->applyExcluedFromUsersFilter($query);
		$this->applyWithoutEmptyFilter($query);

		$list = User::getSelectList($query->column(), false);
		if ($this->to_user_id) {
			unset($list[$this->to_user_id]);
		}
		return $list;
	}

	public function getToUsersList(bool $dateFilter = true): array {
		$query = Provision::find()
			->select('to_user_id')
			->distinct();

		if ($dateFilter) {
			$this->applyDateFilter($query);
		}

		return User::getSelectList($query->column(), false);
	}

	protected function applyDateFilter(ActiveQuery $query): void {
		if (!empty($this->dateFrom) || !empty($this->dateTo)) {
			$column = $this->isUnpaid() ? 'issue_pay.deadline_at' : 'issue_pay.pay_at';
			$query
				->joinWith('pay')
				->andFilterWhere(['>=', $column, $this->dateFrom])
				->andFilterWhere(['<=', $column, $this->dateTo]);
		}
	}

	private function applySettlementFilter(ActiveQuery $query): void {
		if (!empty($this->settlementTypes)) {
			$query->joinWith([
				'pay.calculation PC' => function (IssuePayCalculationQuery $query) {
					$query->onlyTypes($this->settlementTypes);
				},
			]);
		}
	}

	public function applyCustomerNameFilter(QueryInterface $query): void {
		if (!empty($this->customerLastname)) {
			$query->andWhere(['like', 'CP.lastname', $this->customerLastname . '%', false]);
		}
	}

	public static function getPayStatusNames(): array {
		return [
			static::PAY_STATUS_PAID => Yii::t('settlement', 'Paid'),
			static::PAY_STATUS_UNPAID => Yii::t('settlement', 'Unpaid'),
		];
	}

	public static function getSettlementTypesNames(): array {
		return IssuePayCalculation::getTypesNames();
	}

	public static function getTypesNames(): array {
		return ProvisionType::getTypesNames(false);
	}

	private function applyExcluedFromUsersFilter(ProvisionQuery $query): void {
		$query->andFilterWhere([
			'NOT IN', Provision::tableName() . '.from_user_id', $this->excludedFromUsers,
		]);
	}
}
