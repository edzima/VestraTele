<?php

namespace common\modules\lead\chart;

use common\modules\lead\models\LeadStatus;
use yii\base\BaseObject;
use yii\base\StaticInstanceTrait;

class LeadStatusColor extends BaseObject {

	use StaticInstanceTrait;

	public array $colors = ['#008FFB', '#00E396', '#FEB019', '#FF4560', '#775DD0'];
	private array $usedColors = [];

	private array $assignedColors = [];

	public function getStatusColorById(int $id): ?string {
		$model = $this->getModels()[$id] ?? null;
		if ($model) {
			return $this->getStatusColor($model);
		}
		return null;
	}

	public function getStatusColorByGroup(string $name): ?string {
		$model = LeadStatus::findStatusByChartGroup($name);
		if ($model) {
			return $this->getStatusColor($model);
		}
		return null;
	}

	public function getStatusColor(LeadStatus $status): string {
		if ($status->chart_color) {
			return $status->chart_color;
		}
		if (!isset($this->assignedColors[$status->id])) {
			$this->assignedColors[$status->id] = $this->getNextAvailableColor();
		}
		return $this->assignedColors[$status->id];
	}

	public function getNextAvailableColor(): string {
		if (count($this->usedColors) === count($this->colors) - 1) {
			$this->usedColors = [];
		}
		$color = $this->colors[count($this->usedColors) + 1];
		$this->usedColors[] = $color;
		return $color;
	}

	/**
	 * @return LeadStatus[]
	 */
	protected function getModels(): array {
		return LeadStatus::getModels();
	}

}
