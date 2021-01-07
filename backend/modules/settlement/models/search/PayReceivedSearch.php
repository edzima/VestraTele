<?php

namespace backend\modules\settlement\models\search;

use common\models\AgentSearchInterface;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueUser;
use common\models\issue\query\IssueQuery;
use common\models\issue\query\IssueUserQuery;
use common\models\SearchModel;
use common\models\settlement\PayReceived;
use common\models\user\CustomerSearchInterface;
use common\models\user\query\UserQuery;
use common\models\user\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\QueryInterface;

/**
 * PayReceivedSearch represents the model behind the search form of `common\models\settlement\PayReceived`.
 */
class PayReceivedSearch extends PayReceived
	implements
	AgentSearchInterface,
	CustomerSearchInterface,
	SearchModel {

	public $calculationType;
	public $issueAgent;
	public $customerLastname;
	public $value;
	public $transferStatus;

	public const TRANFER_STATUS_NO = 'not';
	public const TRANFER_STATUS_YES = 'yes';

	public static function getTransferStatusNames(): array {
		return [
			static::TRANFER_STATUS_NO => Yii::t('common', 'No'),
			static::TRANFER_STATUS_YES => Yii::t('common', 'Yes'),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['pay_id', 'user_id', 'calculationType'], 'integer'],
			[['date_at', 'transfer_at', 'value', 'transferStatus', 'issueAgent'], 'safe'],
			['customerLastname', 'string', 'min' => CustomerSearchInterface::MIN_LENGTH],
		];
	}

	public function attributeLabels(): array {
		return array_merge(parent::attributeLabels(), [
			'issueAgent' => IssueUser::getTypesNames()[IssueUser::TYPE_AGENT],
			'transferStatus' => Yii::t('settlement', 'Transfer status'),
		]);
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
		$query = PayReceived::find();
		$query->joinWith('user');
		$query->joinWith('pay P');
		$query->joinWith('pay.calculation C');
		$query->joinWith([
			'pay.calculation.issue.customer Customer' => function (UserQuery $query) {
				$query->joinWith('userProfile CP');
			},
		]);

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 100,
			],
		]);

		$this->load($params);

		if (!$this->validate()) {
			return $dataProvider;
		}

		$this->applyAgentsFilters($query);
		$this->applyCustomerSurnameFilter($query);
		$this->applyTransferFilter($query);

		// grid filtering conditions
		$query->andFilterWhere([
			PayReceived::tableName() . '.user_id' => $this->user_id,
			PayReceived::tableName() . '.date_at' => $this->date_at,
			PayReceived::tableName() . '.transfer_at' => $this->transfer_at,
			'C.type' => $this->calculationType,
			'P.value' => $this->value,
		]);

		return $dataProvider;
	}

	public static function getUserNames(): array {
		return User::getSelectList(
			PayReceived::find()->select('user_id')
				->distinct()
				->column()
		);
	}

	public static function getCalculationTypesNames(): array {
		return IssuePayCalculation::getTypesNames();
	}

	public function getAgentsNames(): array {
		return User::getSelectList(PayReceived::find()
			->select('U.user_id')
			->joinWith([
				'pay.calculation.issue.users U' => function (IssueUserQuery $query): void {
					$query->withType(IssueUser::TYPE_AGENT);
				},
			])
			->column());
	}

	public function applyAgentsFilters(QueryInterface $query): void {
		if (!empty($this->issueAgent)) {
			$query->joinWith([
				'pay.calculation.issue' => function (IssueQuery $query): void {
					$query->agents((array) $this->issueAgent);
				},
			]);
		}
	}

	public function applyCustomerSurnameFilter(QueryInterface $query): void {
		if (!empty($this->customerLastname)) {
			$query->andWhere(['like', 'CP.lastname', $this->customerLastname . '%', false]);
		}
	}

	private function applyTransferFilter(QueryInterface $query): void {
		switch ($this->transferStatus) {
			case static::TRANFER_STATUS_YES:
				$query->andWhere('transfer_at IS NOT NULL');
				break;
			case static::TRANFER_STATUS_NO:
				$query->andWhere('transfer_at IS NULL');
		}
	}
}
