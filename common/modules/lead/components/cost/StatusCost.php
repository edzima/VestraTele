<?php

namespace common\modules\lead\components\cost;

use common\modules\lead\models\LeadStatus;

class StatusCost {

	private array $statusCounts = [];
	private float $costSum;

	/**
	 * Status counts indexed by Status ID.
	 *
	 * @param int[] $statusCounts
	 * @return $this
	 */
	public function setStatusCounts(array $statusCounts): self {
		$this->statusCounts = $statusCounts;
		return $this;
	}

	/**
	 * Status counts indexed by Status ID.
	 *
	 * @return int[]
	 */
	public function getStatusCounts(): array {
		return $this->statusCounts;
	}

	public function setCostSum(float $value): self {
		$this->costSum = $value;
		return $this;
	}

	public function getCostSum(): float {
		return $this->costSum;
	}

	public function getTotalCount(): int {
		return array_sum($this->statusCounts);
	}

	public function getStatusCosts(): array {
		$sum = $this->getCostSum();
		if (!isset($sum)) {
			return [];
		}
		$costs = [];
		foreach ($this->statusCounts as $statusId => $count) {
			$costs[$statusId] = $sum / $count;
		}
		return $costs;
	}

	public function getDealStageCost(int $stage): ?float {
		return $this->getDealStagesCosts()[$stage] ?? null;
	}

	public function getDealStagesCosts(): array {
		$sum = $this->getCostSum();
		if (!isset($sum)) {
			return [];
		}
		$stagesCounts = $this->getDealStagesCounts();
		$costs = [];
		foreach ($stagesCounts as $dealStage => $count) {
			$costs[$dealStage] = $sum / $count;
		}
		return $costs;
	}

	public function getDealsCosts(array $stages = []): ?float {
		if (empty($this->getCostSum())) {
			return null;
		}
		$stagesCounts = $this->getDealStagesCounts();
		$count = 0;
		foreach ($stagesCounts as $dealStage => $dealCount) {
			if (empty($stages) || in_array($dealStage, $stages)) {
				$count += $dealCount;
			}
		}
		if ($count === 0) {
			return null;
		}
		return $this->getCostSum() / $count;
	}

	public function getDealStagesCounts(): array {
		$counts = [];
		foreach ($this->statusCounts as $statusId => $count) {
			$dealStage = LeadStatus::getModels()[$statusId]->getDealStage() ?? null;
			if ($dealStage) {
				if (!isset($stagesCounts[$dealStage])) {
					$counts[$dealStage] = 0;
				}
				$counts[$dealStage] += $count;
			}
		}
		return $counts;
	}

	public function getDealStageName(int $dealStage): string {
		return LeadStatus::instance()->dealStagesNames()[$dealStage];
	}
}
