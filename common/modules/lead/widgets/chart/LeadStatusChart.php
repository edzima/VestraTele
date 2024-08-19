<?php

namespace common\modules\lead\widgets\chart;

use common\modules\lead\chart\LeadStatusColor;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\query\LeadQuery;
use common\widgets\charts\ChartsWidget;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;

class LeadStatusChart extends Widget {

	public ?LeadQuery $query;

	public array $statuses = [];

	public array $statusesList = [];

	public bool $grouped = false;

	public bool $orderByCount = true;

	public ?LeadStatusColor $statusColor;

	public array $chartOptions = [
		'legendFormatterAsSeriesWithCount' => true,
		'showDonutTotalLabels' => true,
	];

	public function init() {
		parent::init();

		if (empty($this->statusColor)) {
			$this->statusColor = new LeadStatusColor();
		}
		if (empty($this->statuses) && empty($this->statusesList)) {
			if (empty($this->query)) {
				throw new InvalidConfigException('Query must be set, when statuses & statusesList are empty.');
			}
			$query = clone $this->query;
			$this->statusesList = $query->select('status_id')->column();
		}
		if (!empty($this->statusesList) && empty($this->statues)) {
			$this->statuses = $this->getStatusFromList();
		}
		if ($this->orderByCount) {
			arsort($this->statuses);
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
			$labels[] = $this->grouped ? $key : LeadStatus::getNames()[$key];
			$colors[] = $this->grouped
				? $this->statusColor->getStatusColorByGroup($key)
				: $this->statusColor->getStatusColorById($key);
		}

		$options = array_merge($this->chartOptions, [
			'type' => ChartsWidget::TYPE_DONUT,
			'series' => $data,
			'options' => [
				'labels' => $labels,
				'colors' => $colors,
			],
		]);
		return ChartsWidget::widget($options);
	}

	protected function getStatusFromList(): array {
		$statuses = [];
		foreach ($this->statusesList as $statusId) {
			$index = $statusId;
			if ($this->grouped) {
				$status = LeadStatus::getModels()[$statusId];
				$index = $status->chart_group ?? Yii::t('lead', 'Statuses without group');
			}
			if (!isset($statuses[$index])) {
				$statuses[$index] = 0;
			}
			$statuses[$index]++;
		}
		return $statuses;
	}

}
