<?php

namespace common\modules\lead\models\searches;

use common\helpers\ArrayHelper;
use common\models\SearchModel;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadDialer;
use common\modules\lead\models\LeadDialerType;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\query\LeadDialerQuery;
use Yii;
use yii\data\ActiveDataProvider;

class LeadDialerSearch extends LeadDialer implements SearchModel {

	public bool $onlyToCall = false;
	public bool $leadSourceWithoutDialer = false;
	public bool $leadStatusNotForDialer = false;

	public ?int $typeUserId = null;

	public $leadStatusId;
	public $leadSourceId;
	public string $from_at = '';
	public string $to_at = '';

	public function rules(): array {
		return [
			[['type_id', 'priority', 'leadStatusId', 'leadSourceId', 'lead_id', 'status', 'typeUserId'], 'integer'],
			[['onlyToCall', 'leadSourceWithoutDialer', 'leadStatusNotForDialer'], 'boolean'],
			[['from_at', 'to_at', 'created_at', 'updated_at', 'last_at'], 'safe'],
		];
	}

	public function attributeLabels(): array {
		return array_merge(parent::attributeLabels(), [
			'from_at' => Yii::t('lead', 'From At'),
			'to_at' => Yii::t('lead', 'To At'),
			'onlyToCall' => Yii::t('lead', 'Only to Call'),
			'leadSourceWithoutDialer' => Yii::t('lead', 'Lead Source without Dialer'),
			'leadStatusNotForDialer' => Yii::t('lead', 'Lead Status not for Dialer'),
		]);
	}

	public function search(array $params = []): ActiveDataProvider {
		$query = LeadDialer::find();
		$query->joinWith('lead');
		$query->with('type');
		$query->with('lead.reports');
		$query->with('lead.samePhoneLeads.reports');

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		$this->applyLeadSourceFilter($query);
		$this->applyLeadStatusFilter($query);
		$this->applyToCallFilter($query);
		$this->applyTypeUserIdFilter($query);

		$query->andFilterWhere([
			LeadDialer::tableName() . '.status' => $this->status,
			LeadDialer::tableName() . '.type_id' => $this->type_id,
			LeadDialer::tableName() . '.priority' => $this->priority,
		]);

		return $dataProvider;
	}

	private function applyToCallFilter(LeadDialerQuery $query): void {
		if ($this->onlyToCall) {
			$query->toCall();
		}
	}

	private function applyTypeUserIdFilter(LeadDialerQuery $query) {
		if (!empty($this->typeUserId)) {
			$query->userType($this->typeUserId);
		}
	}

	private function applyLeadSourceFilter(LeadDialerQuery $query) {
		if ($this->leadSourceWithoutDialer) {
			$query->joinWith('lead.leadSource');
			$query->andWhere([LeadSource::tableName() . '.dialer_phone' => null]);
		}
		$query->andFilterWhere([
			Lead::tableName() . '.source_id' => $this->leadSourceId,
		]);
	}

	private function applyLeadStatusFilter(LeadDialerQuery $query) {
		if ($this->leadStatusNotForDialer) {
			$query->joinWith('lead.status');
			$query->andWhere([LeadStatus::tableName() . '.not_for_dialer' => true]);
		}
		$query->andFilterWhere([
			Lead::tableName() . '.status_id' => $this->leadStatusId,
		]);
	}

	public static function getTypesNames(): array {
		return ArrayHelper::map(LeadDialerType::find()->all(), 'id', 'name');
	}

	public static function getLeadSourcesNames(): array {
		$ids = LeadDialer::find()
			->joinWith('lead')
			->select('source_id')
			->distinct()
			->column();

		$names = [];
		foreach (LeadSource::getNames(null, true) as $id => $name) {
			if (in_array($id, $ids)) {
				$names[$id] = $name;
			}
		}
		return $names;
	}

}
