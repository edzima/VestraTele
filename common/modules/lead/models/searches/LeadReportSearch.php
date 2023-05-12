<?php

namespace common\modules\lead\models\searches;

use common\models\user\User;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadAnswer;
use common\modules\lead\models\LeadCampaign;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\query\LeadReportQuery;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Query;

/**
 * LeadReportSearch represents the model behind the search form of `common\modules\lead\models\LeadReport`.
 */
class LeadReportSearch extends LeadReport {

	public $lead_name;
	public $lead_phone;
	public $lead_campaign_id;
	public $lead_source_id;
	public $lead_status_id;
	public $lead_type_id;
	public $lead_user_id;

	public $onlySelf;
	public bool $changedStatus = false;
	public $answersQuestions;
	public $from_at;
	public $to_at;

	public $withoutDeleted;

	private ?array $leadsIds = null;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'lead_id', 'owner_id', 'lead_user_id', 'status_id', 'old_status_id', 'lead_type_id', 'lead_source_id', 'lead_campaign_id', 'lead_status_id'], 'integer'],
			[['!owner_id', 'lead_user_id', '!withoutDeleted'], 'required', 'on' => static::SCENARIO_OWNER],
			[['onlySelf'], 'boolean', 'on' => static::SCENARIO_OWNER],
			[['changedStatus', 'withoutDeleted'], 'boolean'],
			[['withoutDeleted'], 'default', 'value' => null],
			['lead_source_id', 'in', 'range' => array_keys($this->getSourcesNames())],
			['lead_campaign_id', 'in', 'range' => array_keys($this->getCampaignNames())],
			['lead_name', 'string', 'min' => 3],
			[['details', 'created_at', 'updated_at', 'answersQuestions', 'lead_phone', 'from_at', 'to_at'], 'safe'],
		];
	}

	public function attributeLabels(): array {
		return [
			'lead_status_id' => Yii::t('lead', 'Current Status'),
			'changedStatus' => Yii::t('lead', 'Changed Status'),
			'lead_source_id' => Yii::t('lead', 'Source'),
			'lead_campaign_id' => Yii::t('lead', 'Campaign'),
			'from_at' => Yii::t('lead', 'From At'),
			'to_at' => Yii::t('lead', 'To At'),
			'onlySelf' => Yii::t('lead', 'Only Self'),
			'withoutDeleted' => Yii::t('lead', 'With Deleted'),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function scenarios() {
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
	public function search(array $params = []) {
		$query = LeadReport::find()
			->joinWith('lead')
			->joinWith('lead.leadSource')
			->joinWith('owner')
			->joinWith('answers')
			->joinWith('answers.question');

		// add conditions that should always apply here

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
			$query->where('0=1');
			Yii::warning($this->getErrors());
			return $dataProvider;
		}

		$this->applyAnswersFilter($query);
		$this->applyDateFilter($query);
		$this->applyLeadNameFilter($query);
		$this->applyLeadPhoneFilter($query);
		$this->applyStatusesFilter($query);
		$this->applyUserFilter($query);
		$this->applyDeletedFilter($query);

		// grid filtering conditions
		$query->andFilterWhere([
			LeadReport::tableName() . '.id' => $this->id,
			LeadReport::tableName() . '.lead_id' => $this->lead_id,
			Lead::tableName() . '.campaign_id' => $this->lead_campaign_id,
			Lead::tableName() . '.source_id' => $this->lead_source_id,
			Lead::tableName() . '.status_id' => $this->lead_status_id,
			LeadSource::tableName() . '.type_id' => $this->lead_type_id,
		]);

		$query->andFilterWhere(['like', LeadReport::tableName() . '.details', $this->details]);

		return $dataProvider;
	}

	public function getAllLeadsIds(Query $query, bool $refresh = false): array {
		if ($refresh || $this->leadsIds === null) {
			$query = clone $query;
			$query->select(LeadReport::tableName() . '.lead_id');
			$query->distinct();
			$this->leadsIds = $query->column();
		}
		return $this->leadsIds;
	}

	private function applyAnswersFilter(Query $query): void {
		$query->andFilterWhere([
			'like', LeadAnswer::tableName() . '.answer', $this->answersQuestions,
		]);
	}

	private function applyDateFilter(ActiveQuery $query): void {
		if (!empty($this->from_at)) {
			$query->andWhere(['>=', LeadReport::tableName() . '.created_at', date('Y-m-d 00:00:00', strtotime($this->from_at))]);
		}
		if (!empty($this->to_at)) {
			$query->andWhere(['<=', LeadReport::tableName() . '.created_at', date('Y-m-d 23:59:59', strtotime($this->to_at))]);
		}
		$query->andFilterWhere([
			LeadReport::tableName() . '.created_at' => $this->created_at,
			LeadReport::tableName() . '.updated_at' => $this->updated_at,
		]);
	}

	private function applyLeadNameFilter(ActiveQuery $query) {
		if (!empty($this->lead_name)) {
			$query->andWhere(['like', Lead::tableName() . '.name', $this->lead_name]);
		}
	}

	private function applyLeadPhoneFilter(ActiveQuery $query) {
		if (!empty($this->lead_phone)) {
			$query->andWhere(['like', Lead::tableName() . '.phone', $this->lead_phone]);
		}
	}

	private function applyStatusesFilter(Query $query): void {
		if ($this->changedStatus) {
			$query->andWhere(LeadReport::tableName() . '.status_id != ' . LeadReport::tableName() . '.old_status_id');
		}
		$query->andFilterWhere([
			LeadReport::tableName() . '.status_id' => $this->status_id,
			LeadReport::tableName() . '.old_status_id' => $this->old_status_id,
		]);
	}

	private function applyUserFilter(LeadReportQuery $query): void {
		if (empty($this->lead_user_id) || $this->onlySelf) {
			$query->andFilterWhere([
				LeadReport::tableName() . '.owner_id' => $this->owner_id,
			]);
			return;
		}
		$query->joinWith('lead.leadUsers LU');
		$query->groupBy(LeadReport::tableName() . '.id');
		if (empty($this->owner_id)) {
			$query->andWhere(['LU.user_id' => $this->lead_user_id]);
			return;
		}
		$query->andWhere(
			[
				'or',
				['LU.user_id' => $this->lead_user_id],
				[LeadReport::tableName() . '.owner_id' => $this->owner_id],
			]);
	}

	public function getSourcesNames(): array {
		if ($this->getScenario() === static::SCENARIO_OWNER) {
			return LeadSource::getNames($this->owner_id);
		}
		return LeadSource::getNames();
	}

	public function getCampaignNames(): array {
		if ($this->getScenario() === static::SCENARIO_OWNER) {
			return LeadCampaign::getNames($this->owner_id);
		}
		return LeadCampaign::getNames();
	}

	public static function getOwnersNames(): array {
		return User::getSelectList(
			LeadReport::find()
				->select('owner_id')
				->distinct()
				->column(), true);
	}

	private function applyDeletedFilter(LeadReportQuery $query) {
		if ($this->withoutDeleted === null || $this->withoutDeleted === '') {
			return;
		}
		if ($this->withoutDeleted) {
			$query->andWhere(LeadReport::tableName() . '.deleted_at IS NOT NULL');
		} else {
			$query->andWhere(LeadReport::tableName() . '.deleted_at IS NULL');
		}
	}

}
