<?php

namespace backend\modules\issue\models\search;

use common\models\issue\IssueNote;
use common\models\user\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\QueryInterface;

/**
 * IssueNoteSearch represents the model behind the search form of `common\models\issue\IssueNote`.
 */
class IssueNoteSearch extends IssueNote {

	public $dateFrom;
	public $dateTo;
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

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['id', 'issue_id', 'user_id', 'updater_id'], 'integer'],
			[['is_pinned', 'is_template', 'issueGrouped'], 'boolean'],
			[['title', 'description', 'publish_at', 'created_at', 'updated_at', 'type', 'dateFrom', 'dateTo'], 'safe'],
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
		$query->joinWith('user.userProfile');

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

		$this->applyDateFilter($query);

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
}
