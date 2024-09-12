<?php

namespace common\modules\lead\widgets\chart;

use common\helpers\ActiveQueryHelper;
use common\helpers\ArrayHelper;
use common\modules\lead\components\cost\StatusCost;
use common\modules\lead\models\LeadDealStage;
use common\modules\lead\models\query\LeadQuery;
use common\widgets\charts\ChartsWidget;
use Yii;

class LeadUsersCostsChart extends LeadUsersChart {

	public bool $stacked = false;

	public string $statusColumn = 'status_id';

	public ?string $costColumn = 'cost_value';

	public ?array $usersStatusCosts = null;

	public int $roundPrecision = 1;

	public function init(): void {
		parent::init();
		$this->totalTitle = Yii::t('lead', 'Costs');
	}

	public function renderNav(): string {
		return '';
	}

	public function renderUsersStatusChart(): string {
		return $this->renderMultipleUsersChart();
	}

	protected function getSeries(): array {
		$statuses = $this->getUsersStatusCosts();
		$series = [
			'costSum' => [
				'name' => Yii::t('lead', 'Costs'),
				'data' => [],
				'type' => ChartsWidget::TYPE_AREA,
				'group' => 'cost',
			],
			'dealWon' => [
				'name' => Yii::t('lead', 'Deal Stage: Closed Won'),
				'data' => [],
				'type' => ChartsWidget::TYPE_BAR,
				'group' => 'deal',
			],
			'dealContractSent' => [
				'name' => Yii::t('lead', 'Deal Stage: Contract Sent'),
				'data' => [],
				'type' => ChartsWidget::TYPE_BAR,
				'group' => 'deal',
			],
			'dealWonAndSent' => [
				'name' => Yii::t('lead', 'Deal Deals Stages: Closed Won & Contract Sent - Costs'),
				'data' => [],
				'type' => ChartsWidget::TYPE_BAR,
				'group' => 'deal',
			],
		];

		$hasDealWon = false;
		foreach ($statuses as $statusCost) {
			$sum = $statusCost->getCostSum();
			if ($sum) {
				$series['costSum']['data'][] = round($sum, $this->roundPrecision);

				$dealWon = $statusCost->getDealStageCost(LeadDealStage::DEAL_STAGE_CLOSED_WON);
				$dealContractSent = $statusCost->getDealStageCost(LeadDealStage::DEAL_STAGE_CONTRACT_SENT);

				if ($dealWon && $dealContractSent) {
					$dealWonAndSent = $statusCost->getDealsCosts([
						LeadDealStage::DEAL_STAGE_CLOSED_WON,
						LeadDealStage::DEAL_STAGE_CONTRACT_SENT,
					]);
				} else {
					$dealWonAndSent = null;
				}

				$series['dealWon']['data'][] = $dealWon
					? round($dealWon, $this->roundPrecision)
					: null;
				$series['dealContractSent']['data'][] = $dealContractSent
					? round($dealContractSent, $this->roundPrecision)
					: null;
				$series['dealWonAndSent']['data'][] = $dealWonAndSent
					? round($dealWonAndSent, $this->roundPrecision)
					: null;

				if ($dealWon) {
					$hasDealWon = true;
				}
			}
		}
		if (!$hasDealWon) {
			unset($series['dealWon']);
			unset($series['dealWonAndSent']);
		}

		return array_values($series);
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
			$costColumn = $this->costColumn;
			$select = [
				$this->statusColumn,
				$this->userColumn,
				"count($statusColumn) as count",
				"sum($costColumn) as {$this->costColumn}",
			];
			$query->select($select)
				->groupBy([
					$statusColumn,
					$userColumn,
				]);

			$query->having('cost_value is NOT NULL');

			$this->queryData = $query
				->asArray()
				->all();
		}

		return $this->queryData;
	}

	/**
	 * @return StatusCost[] indexed by User ID
	 */
	protected function getUsersStatusCosts(): array {
		if ($this->usersStatusCosts === null) {
			$data = $this->getQueryData();
			$userData = ArrayHelper::index($data, $this->statusColumn, $this->userColumn);
			$usersStatusCosts = [];

			foreach ($userData as $userId => $data) {
				$costs = ArrayHelper::getColumn($data, $this->costColumn);
				$sum = round(array_sum($costs), $this->roundPrecision);
				$statusCounts = array_map('intval', ArrayHelper::map($data, $this->statusColumn, $this->countColumn));
				$statusCosts = new StatusCost();
				$statusCosts->setCostSum($sum);
				$statusCosts->setStatusCounts($statusCounts);
				$usersStatusCosts[$userId] = $statusCosts;
			}
			uasort($usersStatusCosts, function ($a, $b) {
				return $b->getCostSum() <=> $a->getCostSum();
			});
			$this->usersStatusCosts = $usersStatusCosts;
		}
		return $this->usersStatusCosts;
	}

	protected function getTotalData(): array {
		$statusCosts = $this->getUsersStatusCosts();
		$total = [];
		foreach ($statusCosts as $userId => $statusCost) {
			$total[$userId] = round($statusCost->getCostSum(), 0);
		}
		return $total;
	}

}
