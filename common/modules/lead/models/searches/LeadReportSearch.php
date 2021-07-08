<?php

namespace common\modules\lead\models\searches;

use common\models\user\User;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadAnswer;
use common\modules\lead\models\LeadCampaign;
use common\modules\lead\models\LeadSource;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\lead\models\LeadReport;
use yii\db\Query;

/**
 * LeadReportSearch represents the model behind the search form of `common\modules\lead\models\LeadReport`.
 */
class LeadReportSearch extends LeadReport {

	public $lead_campaign_id;
	public $lead_source_id;
	public $lead_type_id;
	public bool $changedStatus = false;
	public $answersQuestions;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'lead_id', 'owner_id', 'status_id', 'old_status_id', 'lead_type_id', 'lead_source_id', 'lead_campaign_id'], 'integer'],
			['!owner_id', 'required', 'on' => static::SCENARIO_OWNER],
			[['changedStatus'], 'boolean'],
			['lead_source_id', 'in', 'range' => array_keys($this->getSourcesNames())],
			['lead_campaign_id', 'in', 'range' => array_keys($this->getCampaignNames())],
			[['details', 'created_at', 'updated_at', 'answersQuestions'], 'safe'],
		];
	}

	public function attributeLabels(): array {
		return [
			'changedStatus' => Yii::t('lead', 'Changed Status'),
			'lead_source_id' => Yii::t('lead', 'Source'),
			'lead_campaign_id' => Yii::t('lead', 'Campaign'),
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
		]);

		$this->load($params);

		if (!$this->validate()) {
			$query->where('0=1');
			return $dataProvider;
		}

		$this->applyAnswersFilter($query);
		$this->applyStatusesFilter($query);

		// grid filtering conditions
		$query->andFilterWhere([
			LeadReport::tableName() . '.id' => $this->id,
			LeadReport::tableName() . '.lead_id' => $this->lead_id,
			LeadReport::tableName() . '.owner_id' => $this->owner_id,
			LeadReport::tableName() . '.created_at' => $this->created_at,
			LeadReport::tableName() . '.updated_at' => $this->updated_at,
			Lead::tableName() . '.campaign_id' => $this->lead_campaign_id,
			Lead::tableName() . '.source_id' => $this->lead_source_id,

		]);

		$query->andFilterWhere(['like', LeadReport::tableName() . '.details', $this->details]);

		return $dataProvider;
	}

	private function applyAnswersFilter(Query $query): void {
		$query->andFilterWhere([
			'like', LeadAnswer::tableName() . '.answer', $this->answersQuestions,
		]);
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

}
