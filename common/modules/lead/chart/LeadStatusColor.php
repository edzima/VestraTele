<?php

namespace common\modules\lead\chart;

use common\modules\lead\models\LeadStatus;

class LeadStatusColor {

	public array $colors = ['#008FFB', '#00E396', '#FEB019', '#FF4560', '#775DD0'];
	private array $usedColors = [];

	private array $assignedColors = [];

	public function getStatusColor(LeadStatus $status): string {
		if ($status->chart_color) {
			return $status->chart_color;
		}
		if (!isset($this->assignedColors[$status->id])) {
			$this->assignedColors[$status->id] = $this->getNextAvailableColor();
		}
		return $this->assignedColors[$status->id];
	}

	protected function getNextAvailableColor(): string {
		if (count($this->usedColors) === count($this->colors) - 1) {
			$this->usedColors = [];
		}
		$color = $this->colors[count($this->usedColors) + 1];
		$this->usedColors[] = $color;
		return $color;
	}

}
