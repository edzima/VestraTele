<?php

namespace common\models\provision;

use common\models\issue\IssuePayCalculation;
use common\models\issue\query\IssuePayCalculationQuery;
use common\models\user\CustomerSearchInterface;
use common\models\user\query\UserQuery;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\QueryInterface;
use yii\helpers\ArrayHelper;

/**
 * ProvisionSearch represents the model behind the search form of `common\models\provision\Provision`.
 */
class ProvisionSearch extends Provision implements CustomerSearchInterface {

	public const PAY_STATUS_PAYED = 'payed';
	public const PAY_STATUS_NOT_PAYED = 'not-payed';

	protected const DEFAULT_PAY_STATUS = self::PAY_STATUS_PAYED;

	public $issue_id;
	public $dateFrom;
	public $dateTo;
	public $customerLastname;

	public $calculationTypes = [];
	public $payStatus;

	public static function getPayStatusNames(): array {
		return [
			static::PAY_STATUS_PAYED => 'Opłacone',
			static::PAY_STATUS_NOT_PAYED => 'Nie opłacone',
		];
	}

	public function isNotPayed(): bool {
		return $this->payStatus === static::PAY_STATUS_NOT_PAYED;
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['hide_on_report'], 'boolean'],
			[['pay_id', 'from_user_id', 'to_user_id', 'issue_id'], 'integer'],
			['type_id', 'in', 'range' => array_keys(static::getTypesNames()), 'allowArray' => true],
			['calculationTypes', 'in', 'range' => array_keys(static::getCalculationTypesNames()), 'allowArray' => true],
			['payStatus', 'in', 'range' => array_keys(static::getPayStatusNames())],
			['payStatus', 'default', 'value' => static::DEFAULT_PAY_STATUS],
			[['dateFrom', 'dateTo'], 'safe'],
			['customerLastname', 'string', 'min' => CustomerSearchInterface::MIN_LENGTH],
		];
	}

	public function attributeLabels(): array {
		return array_merge([
			'onlyPayed' => 'Tylko opłacone',
			'dateFrom' => 'Data od',
			'dateTo' => 'Data do',
			'payStatus' => 'Status płatności',
			'calculationTypes' => Yii::t('settlement', 'Settlement type'),
		], parent::attributeLabels());
	}

	public function init() {
		if (empty($this->dateFrom)) {
			$this->dateFrom = date('Y-m-d', strtotime('first day of this month'));
		}
		if (empty($this->dateTo)) {
			$this->dateTo = date('Y-m-d', strtotime('last day of this month'));
		}
		parent::init();
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
			->with('type')
			->with('fromUser.userProfile')
			->with('toUser.userProfile');

		$query->joinWith([
			'pay.issue.customer C' => function (UserQuery $query) {
				$query->joinWith('userProfile CP');
			},
		]);

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if (!$this->validate()) {
			return $dataProvider;
		}

		$this->applyCustomerSurnameFilter($query);
		$this->applyCalculationTypeFilter($query);

		if (!empty($this->issue_id)) {
			$query->joinWith('pay.issue');
			$query->andWhere(['issue.id' => $this->issue_id]);
		}

		if (!empty($this->payStatus)) {
			$query->joinWith('pay');
			switch ($this->payStatus) {
				case static::PAY_STATUS_NOT_PAYED:
					$query->andWhere('issue_pay.pay_at IS NULL');
					break;
				case static::PAY_STATUS_PAYED:
					$query->andWhere('issue_pay.pay_at IS NOT NULL');
			}
		}

		if ($this->hide_on_report) {
			$query->andWhere(['provision.hide_on_report' => true]);
		}

		// grid filtering conditions
		$query->andFilterWhere([
			Provision::tableName() . '.pay_id' => $this->pay_id,
			Provision::tableName() . '.to_user_id' => $this->to_user_id,
			Provision::tableName() . '.from_user_id' => $this->from_user_id,
			Provision::tableName() . '.type_id' => $this->type_id,
		]);
		$this->dateFilter($query);

		return $dataProvider;
	}

	public function getFromUserList(): array {
		$query = Provision::find()
			->select('from_user_id')
			->groupBy('from_user_id')
			->joinWith('fromUser.userProfile');
		$this->dateFilter($query);
		return ArrayHelper::map($query->all(), 'from_user_id', 'fromUser.fullName');
	}

	public function getToUsersList(): array {
		$query = Provision::find()
			->select('to_user_id')
			->groupBy('to_user_id')
			->joinWith('toUser.userProfile');
		$this->dateFilter($query);
		return ArrayHelper::map($query->all(), 'to_user_id', 'toUser.fullName');
	}

	protected function dateFilter(ActiveQuery $query): void {
		if (!empty($this->dateFrom) || !empty($this->dateTo)) {
			$column = $this->isNotPayed() ? 'issue_pay.deadline_at' : 'issue_pay.pay_at';
			$query
				->joinWith('pay')
				->andFilterWhere(['>=', $column, $this->dateFrom])
				->andFilterWhere(['<=', $column, $this->dateTo]);
		}
	}

	private function applyCalculationTypeFilter(ActiveQuery $query): void {
		if (!empty($this->calculationTypes)) {
			$query->joinWith([
				'pay.calculation PC' => function (IssuePayCalculationQuery $query) {
					$query->onlyTypes($this->calculationTypes);
				},
			]);
		}
	}

	public function applyCustomerSurnameFilter(QueryInterface $query): void {
		if (!empty($this->customerLastname)) {
			$query->andWhere(['like', 'CP.lastname', $this->customerLastname . '%', false]);
		}
	}

	public static function getCalculationTypesNames(): array {
		return IssuePayCalculation::getTypesNames();
	}

	public static function getTypesNames(): array {
		return ProvisionType::getTypesNames();
	}
}
