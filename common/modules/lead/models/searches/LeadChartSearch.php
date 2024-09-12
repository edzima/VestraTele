<?php

namespace common\modules\lead\models\searches;

use common\helpers\ArrayHelper;
use common\helpers\Html;
use common\helpers\Url;
use common\modules\lead\chart\LeadStatusColor;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\LeadUser;
use common\modules\lead\models\query\LeadQuery;
use common\widgets\charts\ChartsWidget;
use DateTime;
use Yii;
use yii\db\Expression;

class LeadChartSearch extends LeadSearch {

	public bool $withoutArchives = false;
	public ?int $groupedStatus = self::STATUS_GROUP_DISABLE;

	public string $groupedStatusChartType = ChartsWidget::TYPE_DONUT;

	public bool $visibleHoursChart = true;

	public const STATUS_GROUP_ONLY_ASSIGNED = 1;
	public const STATUS_GROUP_WITHOUT_ASSIGNED = 2;
	public const STATUS_GROUP_DISABLE = 3;
	private ?LeadStatusColor $leadStatusColor = null;

	public function rules(): array {
		return array_merge([
			[['visibleHoursChart'], 'boolean'],
			['groupedStatus', 'integer'],
			[['groupedStatusChartType'], 'string'],
			['from_at', 'default', 'value' => $this->getDefaultFromAt()],
			['to_at', 'default', 'value' => $this->getDefaultToAt()],
		], parent::rules());
	}

	public function attributeLabels(): array {
		return array_merge(parent::attributeLabels(), [
			'groupedStatus' => Yii::t('lead', 'Grouped Status'),
			'groupedStatusChartType' => Yii::t('lead', 'Grouped Status Chart'),
			'visibleHoursChart' => Yii::t('lead', 'Visible Hours Chart'),
		]);
	}

	public static function statusGroupNames(): array {
		return [
			static::STATUS_GROUP_ONLY_ASSIGNED => Yii::t('lead', 'Status Group only Assigned'),
			static::STATUS_GROUP_WITHOUT_ASSIGNED => Yii::t('lead', 'Status Group Without Assigned'),
			static::STATUS_GROUP_DISABLE => Yii::t('lead', 'Status Group Disabled'),
		];
	}

	public static function statusGroupChartTypesNames(): array {
		return [
			ChartsWidget::TYPE_DONUT => Yii::t('chart', 'Donut'),
			ChartsWidget::TYPE_RADIAL_BAR => Yii::t('chart', 'Radial Bar'),
		];
	}

	public function getDefaultFromAt(): string {
		$currentDate = new DateTime();
		$currentDate->modify('Monday this week');
		return $currentDate->format('Y-m-d');
	}

	public function getDefaultToAt(): string {
		$currentDate = new DateTime();
		$currentDate->modify('Sunday this week');
		return $currentDate->format('Y-m-d');
	}

	public function getLeadsGroupsByHours(): array {
		$query = $this->getBaseQuery();
		$query->select([
			new Expression('HOUR(date_at) as hour'),
			new Expression('COUNT(*) as count'),
			'provider',
		]);
		$query->groupBy([
			'HOUR(date_at)',
			'provider',
		]);
		$query->asArray();
		$data = $query->all();
		return $data;
	}

	public function getLeadsByDays(): array {
		$query = $this->getBaseQuery();
		$query->groupBy([
			Lead::expressionDateAtAsDate(),
			Lead::tableName() . '.status_id',
		]);
		$query->select([
			'count(*) AS count',
			new Expression('DATE(' . Lead::tableName() . '.date_at) as date'),
			Lead::tableName() . '.status_id',
		]);
		$query->orderBy([Lead::tableName() . '.date_at' => SORT_ASC]);
		$query->asArray();
		$data = $query->all();
		uasort($data, function ($a, $b) {
			return LeadStatus::getModels()[$b['status_id']]->sort_index <=> LeadStatus::getModels()[$a['status_id']]->sort_index;
		});
		return $data;
	}

	public function getLeadSourcesCount(): array {
		$query = $this->getBaseQuery();
		$query->groupBy(Lead::tableName() . '.source_id');
		$query->select([Lead::tableName() . '.source_id', 'count(*) as count']);
		$query->asArray();
		$data = $query->all();
		$data = ArrayHelper::map($data, 'source_id', 'count');
		$data = array_map('intval', $data);
		arsort($data);
		return $data;
	}

	public function getLeadTypesCount(): array {
		$query = $this->getBaseQuery();
		$query->joinWith('leadSource');
		$query->groupBy(LeadSource::tableName() . '.type_id');
		$query->select([LeadSource::tableName() . '.type_id', 'count(*) as count']);
		$query->asArray();
		$data = $query->all();
		$data = ArrayHelper::map($data, 'type_id', 'count');
		$data = array_map('intval', $data);
		arsort($data);
		return $data;
	}

	public function getLeadStatusesCount(): array {
		$query = $this->getBaseQuery();
		$query->groupBy(Lead::tableName() . '.status_id');
		$query->select([Lead::tableName() . '.status_id', 'count(*) as count']);
		$query->asArray();
		$data = $query->all();
		$data = ArrayHelper::map($data, 'status_id', 'count');
		$data = array_map('intval', $data);
		uksort($data, function ($a, $b) {
			return LeadStatus::getModels()[$b]->sort_index <=> LeadStatus::getModels()[$a]->sort_index;
		});
		return $data;
	}

	public function getCampaignsData(bool $withCosts = false): array {
		$query = $this->getBaseQuery();
		$query->groupBy(Lead::tableName() . '.campaign_id');
		$select = [
			Lead::tableName() . '.campaign_id',
			'count(*) as count',
		];
		if ($withCosts) {
			$select[] = 'SUM(cost_value) as sumCost';
		}
		$query->select($select);
		$query->asArray();
		$data = $query->all();
		//$data = array_map('floatval', $data);

		return $data;
	}

	private ?array $leadCampaignsCount = null;

	public function getLeadCampaignsCount(bool $refresh = false): array {
		if ($this->leadCampaignsCount === null || $refresh) {
			$query = $this->getBaseQuery();
			$query->groupBy(Lead::tableName() . '.campaign_id');
			$query->select([Lead::tableName() . '.campaign_id', 'count(*) as count']);
			$query->asArray();
			$data = $query->all();
			$data = ArrayHelper::map($data, 'campaign_id', 'count');
			$data = array_map('intval', $data);
			arsort($data);
			$this->leadCampaignsCount = $data;
		}
		return $this->leadCampaignsCount;
	}

	public function getCampaignCost(): array {
		$query = $this->getBaseQuery();
		$query->groupBy(Lead::tableName() . '.campaign_id');
		$query->select(['SUM(cost_value) as sumCost', 'campaign_id']);
		$query->asArray();
		$data = $query->all();
		$data = ArrayHelper::map($data, 'campaign_id', 'sumCost');
		$data = array_map('floatval', $data);
		arsort($data);
		return $data;
	}

	public function getLeadProvidersCount(): array {
		$query = $this->getBaseQuery();
		$query->groupBy(Lead::tableName() . '.provider');
		$query->select([Lead::tableName() . '.provider', 'count(*) as count']);
		$query->asArray();
		$data = $query->all();
		$data = ArrayHelper::map($data, 'provider', 'count');
		$data = array_map('intval', $data);
		return $data;
	}

	public function getLeadsUserStatusData(bool $withCosts): array {
		$query = LeadUser::find();
		$query->joinWith([
			'lead' => function (LeadQuery $query) {
				$this->applyLeadFilter($query);
			},
		]);
		$query = $this->getBaseQuery()->joinWith('leadUsers');
		$query->andWhere([LeadUser::tableName() . '.type' => LeadUser::TYPE_OWNER]);

		$select = [
			LeadUser::tableName() . '.user_id',
			Lead::tableName() . '.status_id',
			'count(*) as count',
		];
		if ($withCosts) {
			$select[] = 'SUM(' . Lead::tableName() . '.cost_value) as costValue';
			$select[] = 'count(' . Lead::tableName() . '.cost_value) as costCount';
		}
		$query->select($select);
		$query->groupBy([
			LeadUser::tableName() . '.user_id',
			Lead::tableName() . '.status_id',
		]);
		$query->distinct();
		$query->asArray();
		$data = $query->all();
		uasort($data, function ($a, $b) {
			return LeadStatus::getModels()[$b['status_id']]->sort_index <=> LeadStatus::getModels()[$a['status_id']]->sort_index;
		});
		return $data;
	}

	public function getBaseQuery(bool $validate = false): LeadQuery {
		$query = Lead::find();
		if ($validate && !$this->validate()) {
			$query->andWhere('0=1');
		}
		$this->applyLeadFilter($query);

		return $query;
	}

	protected function applyLeadFilter(LeadQuery $query): void {
		$this->applyDateFilter($query);
		$this->applyStatusFilter($query);
		$this->applyExcludedStatusFilter($query);
		$this->applyUserFilter($query);
		$this->applyLeadDirectlyFilter($query);
		$this->applyHoursAfterLastReport($query);
		$this->applyFromMarketFilter($query);
		$this->applyReportStatusFilter($query);
		$this->applyTypeFilter($query);
		$this->applyOnlyWithCosts($query);
		$this->applyCampaignFilter($query);
	}

	public function getUniqueId(): string {
		return md5(serialize($this->toArray()));
	}

	public function getLeadStatusColor(): LeadStatusColor {
		if ($this->leadStatusColor === null) {
			$this->leadStatusColor = LeadStatusColor::instance();
		}
		return $this->leadStatusColor;
	}

	public function getLeadsUrl() {
		$params = ['lead/index'];
		foreach ($this->safeAttributes() as $name) {
			$params[Html::getInputName(LeadSearch::instance(), $name)] = $this->getAttribute($name);
		}
		var_dump($this->owner_id);
		echo Html::dump($params);
		return Url::to($params);
	}

}
