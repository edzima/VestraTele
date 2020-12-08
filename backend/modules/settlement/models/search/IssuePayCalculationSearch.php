<?php

namespace backend\modules\settlement\models\search;

use backend\modules\issue\models\IssueStage;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueType;
use common\models\issue\query\IssuePayCalculationQuery;
use common\models\issue\query\IssuePayQuery;
use common\models\issue\query\IssueQuery;
use common\models\SearchModel;
use common\models\user\CustomerSearchInterface;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\QueryInterface;
use yii\helpers\ArrayHelper;

/**
 * IssuePayCalculationSearch represents the model behind the search form of `common\models\issue\IssuePayCalculation`.
 */
class IssuePayCalculationSearch extends IssuePayCalculation implements
	CustomerSearchInterface,
	SearchModel {

	public $issue_type_id;
	public string $customerLastname = '';
	public ?bool $withCustomer = true;
	public ?bool $withoutProvisions = null;

	public ?bool $onlyWithProblems = null;
	public ?bool $onlyWithPayedPays = null;

	private static ?array $STAGES_NAMES = null;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['issue_id', 'stage_id', 'type', 'problem_status'], 'integer'],
			['issue_type_id', 'in', 'range' => IssueType::getTypesIds(), 'allowArray' => true],
			[['value'], 'number'],
			['customerLastname', 'string', 'min' => CustomerSearchInterface::MIN_LENGTH],
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
		$query = IssuePayCalculation::find();
		$query->joinWith('issue');
		$query->joinWith('issue.type IT');

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'defaultOrder' => [
					'updated_at' => SORT_DESC,
				],
			],
		]);

		$this->load($params);

		if (!$this->validate()) {
			return $dataProvider;
		}
		$this->applyCustomerSurnameFilter($query);
		$this->applyProblemStatusFilter($query);
		$this->applyIssueTypeFilter($query);
		$this->applyWithPayedPays($query);
		$this->applyWithoutProvisionsFilter($query);

		// grid filtering conditions
		$query->andFilterWhere([
			IssuePayCalculation::tableName() . '.value' => $this->value,
			IssuePayCalculation::tableName() . '.type' => $this->type,
			IssuePayCalculation::tableName() . '.stage_id' => $this->stage_id,
		]);

		$query->andFilterWhere(['like', 'issue.id', $this->issue_id]);

		return $dataProvider;
	}

	protected function applyWithPayedPays(IssuePayCalculationQuery $query): void {
		if ($this->onlyWithPayedPays) {
			$query->joinWith([
				'pays P' => function (IssuePayQuery $payQuery) {
					$payQuery->onlyPayed();
				},
			]);
		}
	}

	protected function applyProblemStatusFilter(IssuePayCalculationQuery $query): void {
		if ($this->onlyWithProblems === false) {
			$query->onlyWithoutProblems();
		} elseif ($this->onlyWithProblems === true) {
			$query->onlyProblems();
		}
		if (!empty($this->problem_status)) {
			$query->onlyProblems((array) $this->problem_status);
		}
	}

	protected function applyWithoutProvisionsFilter(IssuePayCalculationQuery $query): void {
		if ($this->withoutProvisions) {
			$query->distinct();
			$query->joinWith([
				'pays P' => function (IssuePayQuery $query): void {
					$query->joinWith('provisions PR');
					$query->andWhere('P.id IS NOT NULL');
					$query->andWhere('PR.id IS NULL');
				},
				'issue' => function (IssueQuery $query): void {
					$query->withoutArchives();
				},
			]);
		}
	}

	public function applyCustomerSurnameFilter(QueryInterface $query): void {
		if ($this->withCustomer || !empty($this->customerLastname)) {
			$query->joinWith('issue.customer.userProfile CP');
		}
		if (!empty($this->customerLastname)) {
			$query->andWhere(['like', 'CP.lastname', $this->customerLastname . '%', false]);
		}
	}

	protected function applyIssueTypeFilter(IssuePayCalculationQuery $query) {
		if (!empty($this->issue_type_id)) {
			$query->andWhere(['IT.id' => $this->issue_type_id]);
		}
	}

	public static function getStagesNames(): array {
		if (static::$STAGES_NAMES === null) {
			$ids = IssuePayCalculation::find()
				->groupBy('stage_id')
				->select('stage_id')
				->column();
			$models = IssueStage::find()
				->andWhere(['id' => $ids])
				->asArray()
				->all();
			static::$STAGES_NAMES = ArrayHelper::map($models, 'id', 'name');
		}
		return static::$STAGES_NAMES;
	}

}
