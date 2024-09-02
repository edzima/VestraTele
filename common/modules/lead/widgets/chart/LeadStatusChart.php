<?php

namespace common\modules\lead\widgets\chart;

use common\helpers\ArrayHelper;
use common\modules\lead\chart\LeadStatusColor;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\query\LeadQuery;
use common\widgets\charts\ChartsWidget;
use Yii;
use yii\base\Widget;

class LeadStatusChart extends Widget {

	public ?LeadQuery $query;

	public array $statuses = [];

	public array $statusesList = [];

	public bool $grouping = false;

	public bool $orderByCount = true;

	public ?LeadStatusColor $statusColor;

	public string $chartType = ChartsWidget::TYPE_DONUT;

	public array $chartOptions = [
		'legendFormatterAsSeriesWithCount' => true,
		'showDonutTotalLabels' => true,
	];
	public string $withoutGroupColor = '#010101';
	public bool $orderByStatus = true;

	public function init() {
		parent::init();

		if (empty($this->statusColor)) {
			$this->statusColor = LeadStatusColor::instance();
		}
		if (empty($this->statuses) && empty($this->statusesList)) {
			if (!empty($this->query)) {
				$query = clone $this->query;
				$this->statusesList = $query->select('status_id')->column();
			}
		}
		if (!empty($this->statusesList) && empty($this->statues)) {
			$this->statuses = $this->getStatusFromList();
		}
		if ($this->orderByCount) {
			arsort($this->statuses);
		}
		if ($this->orderByStatus) {
			uksort($this->statuses, function ($a, $b) {
				return LeadStatus::sortIndexByKey($b, $this->grouping) <=> LeadStatus::sortIndexByKey($a, $this->grouping);
			});
		}
	}

	public function run() {
		if (empty($this->statuses)) {
			return '';
		}
		$labels = [];
		$data = [];
		$colors = [];
		foreach ($this->statuses as $key => $count) {
			$data[] = (int) $count;
			$labels[] = $this->grouping ? $key : LeadStatus::getNames()[$key];
			$colors[] = $this->getColor($key);
		}
		if ($this->chartType === ChartsWidget::TYPE_RADIAL_BAR) {
			$data = ChartsWidget::radialDataFromCounts($data);
		}

		$options = ArrayHelper::merge([
			'type' => $this->chartType,
			'series' => $data,
			'options' => [
				'labels' => $labels,
				'colors' => $colors,
			],
		], $this->chartOptions);

		return ChartsWidget::widget($options);
	}

	protected function getColor(string $groupOrStatusId): string {
		if ($this->grouping && $groupOrStatusId === $this->getWithoutChartGroupName()) {
			return $this->withoutGroupColor;
		}
		return $this->grouping
			? $this->statusColor->getStatusColorByGroup($groupOrStatusId)
			: $this->statusColor->getStatusColorById($groupOrStatusId);
	}

	protected function getStatusFromList(): array {
		$statuses = [];
		foreach ($this->statusesList as $statusId) {
			$index = $statusId;
			if ($this->grouping) {
				$status = LeadStatus::getModels()[$statusId];
				$index = $status->chart_group ?? $this->getWithoutChartGroupName();
			}
			if (!isset($statuses[$index])) {
				$statuses[$index] = 0;
			}
			$statuses[$index]++;
		}
		return $statuses;
	}

	protected function getWithoutChartGroupName(): string {
		return Yii::t('lead', 'Statuses without group');
	}

}
