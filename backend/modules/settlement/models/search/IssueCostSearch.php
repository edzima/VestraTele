<?php

namespace backend\modules\settlement\models\search;

use backend\modules\issue\models\IssueStage;
use common\models\issue\IssueCost;
use common\models\issue\IssueType;
use common\models\issue\query\IssueCostQuery;
use common\models\issue\search\IssueTypeSearch;
use common\models\SearchModel;
use common\models\user\User;
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
	public $issueType;
	public $issueStage;

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['id', 'issue_id', 'user_id', 'issueType', 'issueStage'], 'integer'],
			['type', 'string'],
			[['settled', 'withSettlements'], 'boolean'],
			[['created_at', 'updated_at', 'date_at', 'settled_at'], 'safe'],
			[['value', 'vat'], 'number'],
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
		$query->with('issue.stage');

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'defaultOrder' => ['date_at' => SORT_ASC],
			],
		]);

		$this->load($params);
		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

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
			IssueCost::tableName() . '.vat' => $this->vat,
			IssueCost::tableName() . '.created_at' => $this->created_at,
			IssueCost::tableName() . '.date_at' => $this->date_at,
			IssueCost::tableName() . '.settled_at' => $this->date_at,
			IssueCost::tableName() . '.updated_at' => $this->updated_at,
			IssueCost::tableName() . '.type' => $this->type,
		]);

		return $dataProvider;
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

	public static function getIssueTypesNames(): array {
		return IssueType::getShortTypesNames();
	}

	public static function getIssueStagesNames(): array {
		return IssueStage::getStagesNames(true);
	}

}
