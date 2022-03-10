<?php

namespace common\modules\lead\models\searches;

use common\helpers\ArrayHelper;
use common\models\query\PhonableQuery;
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

	public const DESTINATION_EMPTY = 'empty';

	public bool $onlyToCall = false;
	public bool $leadSourceWithoutDialer = false;
	public bool $leadStatusNotForDialer = false;

	public ?int $typeUserId = null;

	public $dialerOrigin;
	public $dialerDestination;

	public $leadName;
	public $leadTypeId;
	public $leadStatusId;
	public $leadSourceId;

	public string $fromLastAt = '';
	public string $toLastAt = '';

	public function rules(): array {
		return [
			[['type_id', 'priority', 'leadStatusId', 'leadSourceId', 'leadTypeId', 'lead_id', 'leadTypeId', 'status', 'typeUserId'], 'integer'],
			[['onlyToCall', 'leadSourceWithoutDialer', 'leadStatusNotForDialer'], 'boolean'],
			[['dialerOrigin', 'dialerDestination', 'leadName'], 'string'],
			[['fromLastAt', 'toLastAt', 'created_at', 'updated_at', 'last_at'], 'safe'],
		];
	}

	public function attributeLabels(): array {
		return array_merge(parent::attributeLabels(), [
			'fromLastAt' => Yii::t('lead', 'From At'),
			'toLastAt' => Yii::t('lead', 'To At'),
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

		$this->applyDialerFilter($query);
		$this->applyLastAtFilter($query);
		$this->applyLeadSourceFilter($query);
		$this->applyLeadStatusFilter($query);
		$this->applyLeadTypeFilter($query);
		$this->applyToCallFilter($query);
		$this->applyTypeUserIdFilter($query);

		$query->andFilterWhere([
			LeadDialer::tableName() . '.status' => $this->status,
			LeadDialer::tableName() . '.type_id' => $this->type_id,
			LeadDialer::tableName() . '.priority' => $this->priority,
		]);

		return $dataProvider;
	}

	private function applyDialerFilter(LeadDialerQuery $query) {
		$this->applyDialerOriginFilter($query);
		$this->applyDialerDestinationFilter($query);
	}

	private function applyDialerOriginFilter(LeadDialerQuery $query): void {
		if (!empty($this->dialerOrigin)) {
			$query->joinWith([
				'lead' => function (PhonableQuery $phonableQuery): void {
					$phonableQuery->withPhoneNumber($this->dialerOrigin);
				},
			]);
		}
	}

	private function applyDialerDestinationFilter(LeadDialerQuery $query): void {
		if (!empty($this->dialerDestination)) {
			$query->joinWith('lead.leadSource');
			if ($this->dialerDestination === static::DESTINATION_EMPTY) {
				$query->andWhere([
					LeadDialer::tableName() . '.destination' => null,

					LeadSource::tableName() . '.dialer_phone' => null,

				]);
			} else {
				$query->andWhere([
					'or',
					[
						LeadDialer::tableName() . '.destination' => $this->dialerDestination,
					],
					[
						LeadSource::tableName() . '.dialer_phone' => $this->dialerDestination,
						LeadDialer::tableName() . '.destination' => null,
					],
				]);
			}
		}
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

	private function applyLeadTypeFilter(LeadDialerQuery $query) {
		if (!empty($this->leadTypeId)) {
			$query->joinWith('lead.leadSource');
			$query->andWhere([
				LeadSource::tableName() . '.type_id' => $this->leadTypeId,
			]);
		}
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

	public static function getDialerDestinationsNames(): array {
		$names = [];
		$names[static::DESTINATION_EMPTY] = Yii::t('lead', 'Without Destination');
		foreach (LeadSource::getModels() as $source) {
			if (!empty($source->dialer_phone)) {
				$names[$source->dialer_phone] = $source->dialer_phone;
			}
		}
		foreach (LeadDialer::find()
			->select('destination')
			->andWhere('destination IS NOT NULL')
			->distinct()
			->column() as $destination) {
			$names[$destination] = $destination;
		}
		asort($names);
		return $names;
	}

	private function applyLastAtFilter(LeadDialerQuery $query): void {
		if (!empty($this->fromLastAt)) {
			$query->andWhere(['>', LeadDialer::tableName() . '.last_at', date(DATE_ATOM, $this->fromLastAt)]);
		}
		if (!empty($this->toLastAt)) {
			$query->andWhere(['<', LeadDialer::tableName() . '.last_at', date(DATE_ATOM, $this->toLastAt)]);
		}
	}

}
