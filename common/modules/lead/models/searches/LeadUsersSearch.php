<?php

namespace common\modules\lead\models\searches;

use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadUser;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * LeadUsersSearch represents the model behind the search form of `common\modules\lead\models\LeadUser`.
 */
class LeadUsersSearch extends LeadUser {

	public $leadStatusId;
	public $leadTypeId;
	public $leadName;

	public $dateFromAt;
	public $dateToAt;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['lead_id', 'user_id', 'leadStatusId', 'leadTypeId'], 'integer'],
			[['firstViewDuration'], 'boolean'],
			[
				[
					'type', 'leadName', 'first_view_at', 'last_view_at',
					'created_at', 'updated_at', 'action_at',
					'dateFromAt', 'dateToAt',
				], 'safe',
			],
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
	public function search(array $params): DataProviderInterface {
		$query = LeadUser::find();
		$query->with('user');
		$query->joinWith('lead');
		$query->joinWith('lead.leadSource');

		$query->select([
				'*',
				new Expression('TIMESTAMPDIFF(SECOND, created_at, first_view_at) as firstViewDuration'),
			]
		);

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$dataProvider->sort->attributes['firstViewDuration'] = [
			'asc' => ['firstViewDuration' => SORT_ASC],
			'desc' => ['firstViewDuration' => SORT_DESC],
		];
		$this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			$query->where('0=1');
			return $dataProvider;
		}

		// grid filtering conditions
		$query->andFilterWhere([
			LeadUser::tableName() . '.lead_id' => $this->lead_id,
			LeadUser::tableName() . '.user_id' => $this->user_id,
			Lead::tableName() . '.status_id' => $this->leadStatusId,
			LeadSource::tableName() . '.type_id' => $this->leadTypeId,

		]);

		$this->applyIsViewedFilter($query);
		$this->applyLeadDateFilter($query);

		$query
			->andFilterWhere([
				'like', 'type', $this->type,
			])->andFilterWhere([
				'like', Lead::tableName() . '.name', $this->leadName,
			]);

		return $dataProvider;
	}

	public function getAvgViewDuration(): array {
		$dataProvider = $this->search([]);
		/** @var ActiveQuery $query */
		$query = $dataProvider->query;
		$query->select([
			new Expression('AVG(TIMESTAMPDIFF(SECOND, created_at, first_view_at)) AS firstViewDuration'),
			new Expression('AVG(TIMESTAMPDIFF(SECOND, first_view_at, last_view_at)) as viewDuration'),
		]);
		$query->asArray();
		return $query->one();
	}

	private function applyIsViewedFilter(ActiveQuery $query): void {
		if ($this->firstViewDuration === '0' || $this->firstViewDuration === '1') {
			if ($this->firstViewDuration) {
				$query->andWhere('first_view_at IS NOT NULL');
			} else {
				$query->andWhere('first_view_at IS NULL');
			}
		}
	}

	private function applyLeadDateFilter(ActiveQuery $query) {
		if (!empty($this->dateFromAt)) {
			$query->andWhere(['>=', Lead::tableName() . '.date_at', date('Y-m-d 00:00:00', strtotime($this->dateFromAt))]);
		}
		if (!empty($this->dateToAt)) {
			$query->andWhere(['<=', Lead::tableName() . '.date_at', date('Y-m-d 23:59:59', strtotime($this->dateToAt))]);
		}
	}
}
