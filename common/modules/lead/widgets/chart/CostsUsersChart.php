<?php

namespace common\modules\lead\widgets\chart;

use common\modules\lead\models\LeadStatus;
use common\widgets\charts\ChartsWidget;
use yii\base\Widget;

class CostsUsersChart extends Widget {

	public array $userCounts = [];

	public array $series = [];

	public function init(): void {
		parent::init();
		if (empty($this->series)) {
			$this->series = $this->seriesFromUsersCount();
		}
	}

	public function seriesFromUsersCount() {
		$costsUsersData = [];
		foreach ($this->userCounts as $userId => $data) {
			if (!empty($data['totalLeadsCostValue'])) {
				foreach ($data['statusCostValue'] as $statusId => $costValue) {
					if (!isset($costsUsersData[$statusId])) {
						$costsUsersData[$statusId] = [
							'name' => LeadStatus::getNames()[$statusId],
							'data' => [],
							'color' => $searchModel->getLeadStatusColor()->getStatusColorById($statusId),
							'type' => ChartsWidget::TYPE_COLUMN,
							'strokeWidth' => 0,
						];
					}
					$count = $costValue['count'];
					if ($count) {
						$value = $costValue['cost'] / $count;
						$name = $data['name'];
						$costsUsersData[$statusId]['costValue'][$userId] = $costValue;
						$costsUsersData[$statusId]['data'][] = [
							'x' => $name,
							'y' => (int) $value,
							'user_id' => $userId,
						];
						$costsUsers[$userId] = $name;
					}
				}
			}
		}
		return $costsUsersData;
	}
}
