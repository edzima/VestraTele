<?php

namespace common\modules\lead\models\searches;

use common\helpers\ArrayHelper;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadCost;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadUser;
use common\modules\lead\models\query\LeadQuery;
use DateTime;
use yii\db\Expression;

class LeadChartSearch extends LeadSearch {

	public function rules(): array {
		return array_merge([
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
		return $query->all();
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
		arsort($data);
		return $data;
	}

	public function getLeadsUsersCount(): array {
		if (!empty($this->user_id) && count((array) $this->user_id) === 1) {
			return [];
		}
//		$query = LeadUser::find();
//		$query->joinWith([
//			'lead' => function (LeadQuery $query) {
//				$this->applyDateFilter($query);
//			},
//		]);
		$query = $this->getBaseQuery();
		$query->joinWith('leadUsers');
		$query->addSelect(['*', 'count(*) as count']);
		$query->groupBy([
			LeadUser::tableName() . '.user_id',
			//	LeadUser::tableName() . '.lead_id',
		]);
		$query->distinct();
		$query->asArray();
		$data = $query->all();
		$data = ArrayHelper::map($data, 'user_id', 'count');
		$data = array_map('intval', $data);
		arsort($data);
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

}
