<?php

namespace backend\modules\issue\models\search;

use backend\modules\issue\models\IssueStage;
use common\models\AgentSearchInterface;
use common\models\issue\Issue;
use common\models\issue\IssueNote;
use common\models\issue\IssueType;
use common\models\issue\IssueUser;
use common\models\issue\query\IssueQuery;
use common\models\issue\search\IssueStageSearchable;
use common\models\issue\search\IssueTypeSearch as IssueTypeSearchable;
use common\models\user\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\QueryInterface;

/**
 * IssueNoteSearch represents the model behind the search form of `common\models\issue\IssueNote`.
 */
class IssueNoteSearch extends IssueNote implements
	AgentSearchInterface,
	IssueStageSearchable,
	IssueTypeSearchable {

	public const SCENARIO_USER = 'user';

	public $agent_id;
	public $dateFrom;
	public $dateTo;

	public $issueTypeId;
	public $issueStageId;
	public $issueGrouped;

	public static function getUsersNames(): array {
		return User::getSelectList(
			IssueNote::find()
				->select('user_id')
				->distinct()
				->column(),
			false
		);
	}

	public static function getUpdatersNames(): array {
		return User::getSelectList(
			IssueNote::find()
				->select('updater_id')
				->distinct()
				->column(),
			false
		);
	}

	public function getAgentsNames(): array {
		return User::getSelectList(
			IssueUser::find()
				->select('user_id')
				->withType(IssueUser::TYPE_AGENT)
				->distinct()
				->column(),
			false
		);
	}

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['id', 'issue_id', 'user_id', 'updater_id'], 'integer'],
			['!user_id', 'required', 'on' => static::SCENARIO_USER],
			[['is_pinned', 'is_template', 'issueGrouped'], 'boolean'],
			[['title', 'description', 'publish_at', 'created_at', 'updated_at', 'type', 'dateFrom', 'dateTo'], 'safe'],
			['agent_id', 'in', 'range' => array_keys($this->getAgentsNames()), 'allowArray' => true],
			['issueStageId', 'in', 'range' => array_keys($this->getIssueStagesNames()), 'allowArray' => true],
			['issueTypeId', 'in', 'range' => array_keys($this->getIssueTypesNames()), 'allowArray' => true],
		];
	}

	public function attributeLabels(): array {
		return array_merge(parent::attributeLabels(), [
				'dateFrom' => Yii::t('common', 'Date from'),
				'dateTo' => Yii::t('common', 'Date to'),
				'issueGrouped' => Yii::t('common', 'Issue Grouped'),
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
		$query = IssueNote::find();
		$query->joinWith('issue');
		$query->joinWith('issue.agent.userProfile');
		$query->joinWith('user.userProfile');
		$query->joinWith('updater.userProfile');
		$query->with('issue.tags');

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'defaultOrder' => [
					'updated_at' => SORT_DESC,
					'created_at' => SORT_DESC,
				],
			],
		]);

		$this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		$this->applyAgentsFilters($query);
		$this->applyDateFilter($query);
		$this->applyIssueTypeFilter($query);
		$this->applyIssueStageFilter($query);

		if ($this->issueGrouped) {
			$query->groupBy(IssueNote::tableName() . '.issue_id');
		}

		// grid filtering conditions
		$query->andFilterWhere([
			IssueNote::tableName() . '.id' => $this->id,
			IssueNote::tableName() . '.issue_id' => $this->issue_id,
			IssueNote::tableName() . '.user_id' => $this->user_id,
			IssueNote::tableName() . '.updater_id' => $this->updater_id,
			IssueNote::tableName() . '.publish_at' => $this->publish_at,
			IssueNote::tableName() . '.created_at' => $this->created_at,
			IssueNote::tableName() . '.updated_at' => $this->updated_at,
			IssueNote::tableName() . '.is_pinned' => $this->is_pinned,
			IssueNote::tableName() . '.is_template' => $this->is_template,
		]);

		$query->andFilterWhere(['like', IssueNote::tableName() . '.title', $this->title])
			->andFilterWhere(['like', IssueNote::tableName() . '.type', $this->type])
			->andFilterWhere(['like', IssueNote::tableName() . '.description', $this->description]);

		return $dataProvider;
	}

	protected function applyDateFilter(QueryInterface $query): void {

		if (!empty($this->dateFrom)) {
			$query->andFilterWhere([
				'>=', IssueNote::tableName() . '.publish_at',
				date('Y-m-d 00:00:00', strtotime($this->dateFrom)),
			]);
		}

		if (!empty($this->dateTo)) {
			$query->andFilterWhere([
				'<=', IssueNote::tableName() . '.publish_at',
				date('Y-m-d 23:59:59', strtotime($this->dateTo)),
			]);
		}
	}

	public function getIssueStagesNames(): array {
		return IssueStage::getStagesNames(true, true);
	}

	public function applyIssueStageFilter(QueryInterface $query): void {
		if (!empty($this->issueStageId)) {
			$query->andWhere([Issue::tableName() . '.stage_id' => $this->issueStageId]);
		}
	}

	public function getIssueTypesNames(): array {
		return IssueType::getTypesNames();
	}

	public function applyIssueTypeFilter(QueryInterface $query): void {
		if (!empty($this->issueTypeId)) {
			$query->andWhere([Issue::tableName() . '.type_id' => $this->issueTypeId]);
		}
	}

	public function applyAgentsFilters(QueryInterface $query): void {
		if (!empty($this->agent_id)) {
			$query->joinWith([
				'issue' => function (IssueQuery $query): void {
					$query->agents($this->agent_id);
				},
			]);
		}
	}
}
