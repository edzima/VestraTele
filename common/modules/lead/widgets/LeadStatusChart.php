<?php

namespace common\modules\lead\widgets;

use common\modules\lead\chart\LeadStatusColor;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\query\LeadQuery;
use common\widgets\charts\ChartsWidget;
use yii\base\InvalidConfigException;
use yii\base\Widget;

class LeadStatusChart extends Widget {

	public ?LeadQuery $query;

	public array $statuses = [];

	public array $statusesList = [];

	public ?LeadStatusColor $statusColor;

	public array $chartOptions = [
		'height' => '300px',
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
			$this->statusesList = $this->query->select('status_id')->column();
		}
		if (!empty($this->statusesList) && empty($this->statues)) {
			foreach ($this->statusesList as $statusId) {
				if (!isset($this->statuses[$statusId])) {
					$this->statuses[$statusId] = 0;
				}
				$this->statuses[$statusId]++;
			}
		}
	}

	public function run() {

		if (empty($this->statuses)) {
			return '';
		}
		$labels = [];
		$data = [];
		$colors = [];
		foreach ($this->statuses as $statusId => $count) {
			$labels[] = LeadStatus::getNames()[$statusId];
			$data[] = (int) $count;
			$colors[] = $this->statusColor->getStatusColorById($statusId);
		}

		$options = array_merge($this->chartOptions, [
			'type' => ChartsWidget::TYPE_PIE,
			'series' => $data,
			'options' => [
				'labels' => $labels,
				'colors' => $colors,
			],
		]);
		return ChartsWidget::widget($options);
	}
}
