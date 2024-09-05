<?php

namespace common\modules\lead\widgets\chart;

use common\helpers\ActiveQueryHelper;
use common\modules\lead\chart\LeadStatusColor;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\query\LeadQuery;
use common\widgets\charts\ChartsWidget;
use Yii;

class LeadUsersStatusChart extends LeadUsersChart {

	public bool $groupStatus = false;

	public string $statusColumn = 'status_id';

	public ?LeadStatusColor $statusColor;

	public function init(): void {
		parent::init();
		if (empty($this->statusColor)) {
			$this->statusColor = new LeadStatusColor();
		}
	}

	protected function getSeries(): array {
		if (empty($this->series)) {
			$data = $this->getQueryData();
			$seriesData = [];
			foreach ($data as $item) {
				$status_id = $item[$this->statusColumn];
				$userId = $item[$this->userColumn];
				$count = $item[$this->countColumn] ? (int) $item[$this->countColumn] : null;

				$statusOrGroupId = $this->groupStatus
					? LeadStatus::getModels()[$status_id]->chart_group
					: $status_id;
				if ($this->groupStatus && empty($statusOrGroupId)) {
					$statusOrGroupId = Yii::t('lead', 'Statuses without group');
				}
				if (!isset($seriesData[$statusOrGroupId])) {
					$status = LeadStatus::getModels()[$status_id];
					$seriesData[$statusOrGroupId] = [
						'name' => $this->groupStatus ? $statusOrGroupId : $status->name,
						'data' => [],
						'type' => ChartsWidget::TYPE_COLUMN,
						'color' => $this->statusColor->getStatusColor($status),
						'sortIndex' => $status->sort_index,
					];
				}
				$seriesData[$statusOrGroupId]['data'][$userId] = $count;
			}

			$total = $this->getTotalData();
			$ownersIds = array_keys($total);
			foreach ($seriesData as $key => $series) {
				$sameTotalOrderData = [];
				foreach ($ownersIds as $ownerId) {
					$count = $series['data'][$ownerId] ?? null;
					$sameTotalOrderData[] = $count;
				}

				$seriesData[$key]['data'] = $sameTotalOrderData;
			}
			usort($seriesData, function (array $a, array $b) {
				return $b['sortIndex'] <=> $a['sortIndex'];
			});
			$this->series = $seriesData;
		}
		return $this->series;
	}

	protected function getQueryData(): array {
		if ($this->queryData === null) {
			$query = clone $this->query;
			if ($query instanceof LeadQuery) {
				if (!ActiveQueryHelper::hasAlreadyJoinedWithRelation($query, 'leadUsers')) {
					$query->joinWith('leadUsers cLu');
					$query->andFilterWhere(['cLu.type' => $this->userTypes]);
				}
			}

			$statusColumn = $this->statusColumn;
			$userColumn = $this->userColumn;
			$select = [
				$this->statusColumn,
				$this->userColumn,
				"count($statusColumn) as count",
			];
			$query->select($select)
				->groupBy([
					$statusColumn,
					$userColumn,
				]);

			$this->queryData = $query
				->asArray()
				->all();
		}

		return $this->queryData;
	}

	protected function getTotalData(): array {
		$data = $this->getQueryData();
		$total = [];
		foreach ($data as $item) {
			$ownerId = $item[$this->userColumn];
			if (!isset($total[$ownerId])) {
				$total[$ownerId] = 0;
			}
			$total[$ownerId] += $item[$this->countColumn];
		}
		arsort($total);
		return $total;
	}

}
