<?php

namespace common\modules\lead\widgets\chart;

use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadReport;
use Yii;
use yii\base\InvalidConfigException;

class LeadReportStatusChart extends LeadUsersStatusChart {

	public const STATUS_REPORT_STATUS_NEW = 'report-status';
	public const STATUS_REPORT_STATUS_OLD = 'old-status';
	public const STATUS_LEAD_STATUS = 'lead-status';

	public string $userColumn = 'owner_id';

	public string $statusType = self::STATUS_REPORT_STATUS_NEW;

	public function init(): void {
		parent::init();
		$this->statusColumn = $this->getStatusColumn();
		$this->totalTitle = Yii::t('lead', 'Reports');
	}

	protected function getQueryData(): array {
		if ($this->queryData === null) {
			$query = clone $this->query;
			$statusColumn = $this->statusColumn;
			if ($this->statusType === static::STATUS_LEAD_STATUS) {
				$query->joinWith('lead', false);
				$statusColumn = Lead::tableName() . '.' . $statusColumn;
			} else {
				$statusColumn = LeadReport::tableName() . '.' . $statusColumn;
			}
			$query->select([
				$statusColumn,
				LeadReport::tableName() . '.owner_id',
				"count($statusColumn) as count",
			])
				->groupBy([
					$statusColumn,
					LeadReport::tableName() . '.owner_id',
				]);

			$this->queryData = $query
				->asArray()
				->all();
		}
		return $this->queryData;
	}

	protected function getAreaChartId(): string {
		return parent::getAreaChartId() . '-' . $this->statusType;
	}

	protected function getDonutChartId(): string {
		return parent::getDonutChartId() . '-' . $this->statusType;
	}

	protected function getStatusColumn(): string {
		switch ($this->statusType) {
			case static::STATUS_REPORT_STATUS_NEW:
			case static::STATUS_LEAD_STATUS:
				return 'status_id';
			case static::STATUS_REPORT_STATUS_OLD:
				return 'old_status_id';
		}
		throw new InvalidConfigException('Invalid status type.');
	}
}

