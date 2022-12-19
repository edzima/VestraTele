<?php

namespace backend\modules\settlement\models\search;

use backend\modules\issue\models\IssueStage;
use common\models\issue\IssueCost;
use common\models\issue\IssueType;
use common\models\issue\query\IssueCostQuery;
use common\models\issue\search\IssueTypeSearch;
use common\models\SearchModel;
use common\models\user\User;
use kartik\daterange\DateRangeBehavior;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\QueryInterface;

/**
 * IssueCostSearch represents the model behind the search form of `common\models\issue\IssueCost`.
 */
class IssueCostSearch extends IssueCost implements SearchModel, IssueTypeSearch {

	public $settled;
	public $withSettlements;
	public $is_confirmed;
	public $issueType;
	public $issueStage;

	public $dateRange;
	public $dateStart;
	public $dateEnd;

	public $deadlineRange;
	public $deadlineStart;
	public $deadlineEnd;

	public $settledRange;
	public $settledStart;
	public $settledEnd;
	public $hide_on_report;

	public function behaviors(): array {
		return [
			'date-range' => [
				'class' => DateRangeBehavior::class,
				'attribute' => 'dateRange',
				'dateStartAttribute' => 'dateStart',
				'dateEndAttribute' => 'dateEnd',
				'dateStartFormat' => 'Y-m-d 00:00:00',
				'dateEndFormat' => 'Y-m-d 23:59:59',
			],
			'deadline-range' => [
				'class' => DateRangeBehavior::class,
				'attribute' => 'deadlineRange',
				'dateStartAttribute' => 'deadlineStart',
				'dateEndAttribute' => 'deadlineEnd',
				'dateStartFormat' => 'Y-m-d 00:00:00',
				'dateEndFormat' => 'Y-m-d 23:59:59',
			],
			'settled-range' => [
				'class' => DateRangeBehavior::class,
				'attribute' => 'settledRange',
				'dateStartAttribute' => 'settledStart',
				'dateEndAttribute' => 'settledEnd',
				'dateStartFormat' => 'Y-m-d 00:00:00',
				'dateEndFormat' => 'Y-m-d 23:59:59',
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['id', 'issue_id', 'user_id'], 'integer'],
			[['type', 'transfer_type'], 'string'],
			[['settled', 'withSettlements', 'is_confirmed', 'hide_on_report'], 'boolean'],
			[['created_at', 'updated_at', 'date_at', 'settled_at'], 'safe'],
			[['dateRange', 'deadlineRange', 'settledRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
			[['value', 'base_value', 'vat'], 'number'],
			['issueType', 'in', 'range' => array_keys($this->getIssueTypesNames()), 'allowArray' => true],
			['issueStage', 'in', 'range' => array_keys(static::getIssueStagesNames()), 'allowArray' => true],

		];
	}

	public function attributeLabels(): array {
		return array_merge(
			parent::attributeLabels(),
			[
				'settled' => Yii::t('settlement', 'Settled'),
				'withSettlements' => Yii::t('settlement', 'With settlements'),
			]
		);
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
		$query = IssueCost::find();
		$query->joinWith(['issue', 'settlements', 'user']);
		$query->with('issue.type');

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'defaultOrder' => ['date_at' => SORT_ASC],
			],
		]);
		$sort = $dataProvider->getSort();
		$sort->attributes['dateRange'] = $sort->attributes['date_at'];
		$sort->attributes['deadlineRange'] = $sort->attributes['deadline_at'];
		$sort->attributes['settledRange'] = $sort->attributes['settled_at'];

		$this->load($params);
		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		$this->applyConfirmedFilter($query);
		$this->applyDatesFilter($query);
		$this->applyIssueStageFilter($query);
		$this->applyIssueTypeFilter($query);
		$this->applySettledFilter($query);
		$this->applyWithSettlementsFilter($query);

		// grid filtering conditions
		$query->andFilterWhere([
			IssueCost::tableName() . '.id' => $this->id,
			IssueCost::tableName() . '.issue_id' => $this->issue_id,
			IssueCost::tableName() . '.user_id' => $this->user_id,
			IssueCost::tableName() . '.value' => $this->value,
			IssueCost::tableName() . '.base_value' => $this->base_value,
			IssueCost::tableName() . '.vat' => $this->vat,
			IssueCost::tableName() . '.type' => $this->type,
			IssueCost::tableName() . '.transfer_type' => $this->transfer_type,
			IssueCost::tableName() . '.hide_on_report' => $this->hide_on_report,
		]);

		return $dataProvider;
	}

	private function applyConfirmedFilter(IssueCostQuery $query): void {
		if ($this->is_confirmed === null || $this->is_confirmed === '') {
			return;
		}
		if ($this->is_confirmed) {
			$query->andWhere(IssueCost::tableName() . '.confirmed_at IS NOT NULL');
			return;
		}
		$query->andWhere(IssueCost::tableName() . '.confirmed_at IS NULL');
	}

	private function applyDatesFilter(IssueCostQuery $query): void {
		$query->andFilterWhere(['>=', IssueCost::tableName() . '.date_at', $this->dateStart])
			->andFilterWhere(['<', IssueCost::tableName() . '.date_at', $this->dateEnd])
			->andFilterWhere(['>=', IssueCost::tableName() . '.deadline_at', $this->deadlineStart])
			->andFilterWhere(['<', IssueCost::tableName() . '.deadline_at', $this->deadlineEnd])
			->andFilterWhere(['>=', IssueCost::tableName() . '.deadline_at', $this->settledStart])
			->andFilterWhere(['<', IssueCost::tableName() . '.deadline_at', $this->settledEnd]);
	}

	private function applySettledFilter(IssueCostQuery $query): void {
		if ($this->settled === null || $this->settled === '') {
			return;
		}
		if ($this->settled) {
			$query->settled();
			return;
		}
		$query->notSettled();
	}

	private function applyWithSettlementsFilter(IssueCostQuery $query): void {
		if ($this->withSettlements === null || $this->withSettlements === '') {
			return;
		}
		if ($this->withSettlements) {
			$query->withSettlements();
			return;
		}
		$query->withoutSettlements();
	}

	private function applyIssueStageFilter(IssueCostQuery $query): void {
		if (!empty($this->issueStage)) {
			$query->andWhere(['issue.stage_id' => $this->issueStage]);
		}
	}

	public function applyIssueTypeFilter(QueryInterface $query): void {
		if (!empty($this->issueType)) {
			$query->andWhere(['issue.type_id' => $this->issueType]);
		}
	}

	public static function getUsersNames(): array {
		return User::getSelectList(IssueCost::find()
			->select('user_id')
			->distinct()
			->column(), false);
	}

	public function getIssueTypesNames(): array {
		return IssueType::getShortTypesNames();
	}

	public static function getIssueStagesNames(): array {
		return IssueStage::getStagesNames(true);
	}

}
