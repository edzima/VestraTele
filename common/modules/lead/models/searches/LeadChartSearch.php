<?php

namespace common\modules\lead\models\searches;

use common\helpers\ArrayHelper;
use common\modules\lead\chart\LeadStatusColor;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadCost;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\LeadUser;
use common\modules\lead\models\query\LeadQuery;
use common\widgets\charts\ChartsWidget;
use DateTime;
use Yii;
use yii\db\Expression;

class LeadChartSearch extends LeadSearch {

	public ?int $groupedStatus = self::STATUS_GROUP_ONLY_ASSIGNED;

	public string $groupedStatusChartType = ChartsWidget::TYPE_DONUT;

	public const STATUS_GROUP_ONLY_ASSIGNED = 1;
	public const STATUS_GROUP_WITHOUT_ASSIGNED = 2;
	public const STATUS_GROUP_DISABLE = 3;
	private ?LeadStatusColor $leadStatusColor = null;

	public function attributeLabels(): array {
		return array_merge(parent::attributeLabels(), [
			'groupedStatus' => Yii::t('lead', 'Grouped Status'),
			'groupedStatusChartType' => Yii::t('lead', 'Grouped Status Chart'),
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

	public function rules(): array {
		return array_merge([
			['groupedStatus', 'integer'],
			[['groupedStatusChartType'], 'string'],
			['from_at', 'default', 'value' => $this->getDefaultFromAt()],
			['to_at', 'default', 'value' => $this->getDefaultToAt()],
		], parent::rules());
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

	public function getLeadsByDays(): array {
		$query = $this->getBaseQuery();
		$query->joinWith('costs');
		$query->groupBy([
			new Expression('DATE(' . Lead::tableName() . '.date_at)'),
			Lead::tableName() . '.status_id',
		]);
		$query->select([
			'count(*) AS count',
			new Expression('DATE(' . Lead::tableName() . '.date_at) as date'),
			Lead::tableName() . '.status_id',
			new Expression('SUM(' . LeadCost::tableName() . '.value)'),
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

	public function getLeadsUserStatusData(): array {
		$query = LeadUser::find();
		$query->joinWith([
			'lead' => function (LeadQuery $query) {
				$this->applyLeadFilter($query);
			},
		]);
		$query = $this->getBaseQuery()->joinWith('leadUsers');
		$query->andWhere([LeadUser::tableName() . '.type' => LeadUser::TYPE_OWNER]);

		$query->select([
			LeadUser::tableName() . '.user_id',
			Lead::tableName() . '.status_id',
			'count(*) as count',
		]);
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

	protected function getBaseQuery(bool $validate = false): LeadQuery {
		$query = Lead::find();
		if ($validate && !$this->validate()) {
			$query->andWhere('0=1');
		}
		$this->applyLeadFilter($query);

		return $query;
	}

	protected function applyLeadFilter(LeadQuery $query): void {
		$this->applyDateFilter($query);
		$this->applyExcludedStatusFilter($query);
		$this->applyUserFilter($query);
		$this->applyLeadDirectlyFilter($query);
		$this->applyHoursAfterLastReport($query);
		$this->applyFromMarketFilter($query);
		$this->applyReportStatusFilter($query);
		$this->applyTypeFilter($query);
	}

	public function getUniqueId(): string {
		return md5(serialize($this->toArray()));
	}

	public function getLeadStatusColor(): LeadStatusColor {
		if ($this->leadStatusColor === null) {
			$this->leadStatusColor = new LeadStatusColor();
		}
		return $this->leadStatusColor;
	}

}