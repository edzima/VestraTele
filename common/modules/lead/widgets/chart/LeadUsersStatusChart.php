<?php

namespace common\modules\lead\widgets\chart;

use common\helpers\ActiveQueryHelper;
use common\helpers\ArrayHelper;
use common\models\user\User;
use common\modules\lead\chart\LeadStatusColor;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\LeadUser;
use common\modules\lead\models\query\LeadQuery;
use common\widgets\charts\ChartsWidget;
use Yii;
use yii\base\Widget;
use yii\db\ActiveQuery;

class LeadUsersStatusChart extends Widget {

	public array $series = [];

	public string $height = '480px';

	public ActiveQuery $query;

	public ?array $queryData = null;
	public bool $groupStatus = false;

	public array $usersNames = [];

	public ?array $usersIds = null;
	public string $userColumn = 'user_id';

	public string $statusColumn = 'status_id';
	public string $countColumn = 'count';

	public ?LeadStatusColor $statusColor;

	public ?string $areaGroup = null;
	public ?string $emptyUserName = null;
	public ?string $notfoundUserName = null;

	public ?string $totalTitle = null;
	private array $userTypes = [
		LeadUser::TYPE_OWNER,
	];

	public function init(): void {
		parent::init();
		if (empty($this->statusColor)) {
			$this->statusColor = new LeadStatusColor();
		}
		if ($this->emptyUserName === null) {
			$this->emptyUserName = Yii::t('lead', 'Without User');
		}
		if ($this->notfoundUserName === null) {
			$this->notfoundUserName = Yii::t('lead', 'Not found User');
		}
		if ($this->totalTitle === null) {
			$this->totalTitle = Yii::t('lead', 'Leads');
		}
	}

	public function run() {
		if (!$this->shouldRender()) {
			return '';
		}
		$renderNavAndTotal = count($this->getTotalData()) > 1;
		return $this->render('lead-user-status', [
			'nav' => $renderNavAndTotal ? $this->renderNav() : '',
			'donutUsersChart' => $renderNavAndTotal ? $this->renderDonutUsersChart() : '',
			'areaUsersStatusChart' => $this->renderUsersStatusChart(),
		]);
	}

	protected function renderNav(): string {
		return NavChart::widget([
			'series' => $this->getSeries(),
			'chartID' => $this->getDonutUsersStatusesChartId(),
			'chartToggleId' => $this->getAreaChartId(),
		]);
	}

	public function renderDonutUsersChart(): string {
		$total = $this->getTotalData();
		$ids = array_keys($total);
		$labels = $this->getUsersNamesFromIds($ids);
		return ChartsWidget::widget([
			'type' => ChartsWidget::TYPE_DONUT,
			'series' => array_values($total),
			'height' => $this->height,
			'showDonutTotalLabels' => true,
			'legendFormatterAsSeriesWithCount' => true,
			'chart' => [
				'id' => $this->getDonutUsersStatusesChartId(),
			],
			'options' => [
				'labels' => $labels,
				'legend' => [
					'show' => false,
				],
				'title' => [
					'text' => $this->totalTitle,
					'align' => 'center',
				],

			],
		]);
	}

	public function renderUsersStatusChart(): string {
		$total = $this->getTotalData();
		$usersCount = count($total);
		if ($usersCount === 0) {
			return '';
		}
		if ($usersCount === 1) {
			return $this->renderSingleUserStatusesChart();
		}
		return $this->renderMultipleUsersStatusesChart();
	}

	protected function renderSingleUserStatusesChart(int $index = 0): string {
		$series = $this->getSeries();
		$colors = ArrayHelper::getColumn($series, 'color', []);
		$labels = ArrayHelper::getColumn($series, 'name', []);
		$seriesArrayData = ArrayHelper::getColumn($series, 'data', []);
		$data = [];
		foreach ($seriesArrayData as $seriesData) {
			$data[] = $seriesData[$index];
		}
		return ChartsWidget::widget([
			'type' => ChartsWidget::TYPE_DONUT,
			'series' => $data,
			'options' => [
				'labels' => $labels,
				'colors' => $colors,
				'title' => [
					'text' => $this->totalTitle,
					'align' => 'center',
				],
			],
			'height' => '400px',
			'legendFormatterAsSeriesWithCount' => true,
			'showDonutTotalLabels' => true,
		]);
	}

	protected function renderMultipleUsersStatusesChart(): string {
		$series = array_values($this->getSeries());
		$labels = $this->getUsersNamesFromIds(
			array_keys($this->getTotalData())
		);
		return ChartsWidget::widget([
			'type' => ChartsWidget::TYPE_AREA,
			'height' => $this->height,
			'series' => $series,
			'chart' => [
				'stacked' => true,
				'id' => $this->getAreaChartId(),
				'group' => $this->areaGroup,
			],
			'options' => [
				'labels' => $labels,
				'stroke' => [
					'width' => 0,
				],
				'plotOptions' => [
					'bar' => [
						'dataLabels' => [
							'total' => [
								'enabled' => true,
							],
						],
					],
				],
			],
		]);
	}

	protected function getSeries(): array {
		if (empty($this->series)) {
			$data = $this->getQueryData();
			$seriesData = [];
			foreach ($data as $item) {
				$status_id = $item[$this->statusColumn];
				$userId = $item[$this->userColumn];
				$count = $item['count'] ? (int) $item['count'] : null;

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
			$query->select([
				$statusColumn,
				$userColumn,
				"count($statusColumn) as count",
			])
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

	protected function shouldRender(): bool {
		return !$this->allSeriesHasEmptyData();
	}

	protected function allSeriesHasEmptyData(): bool {
		$series = $this->getSeries();
		$empty = array_filter($series, function (array $item) {
			return empty($item['data']);
		});
		return count($empty) === count($series);
	}

	protected function getUsersNamesFromIds(array $ids): array {
		$names = [];
		foreach ($ids as $ownerId) {
			$names[] = $this->getUserName($ownerId);
		}
		return $names;
	}

	protected function getUserName($userId): ?string {
		if (empty($userId)) {
			return $this->emptyUserName;
		}
		return $this->getUsersNames()[$userId] ?? $this->notfoundUserName;
	}

	protected function getUsersNames(): array {
		if (empty($this->usersNames)) {
			$ids = array_unique(ArrayHelper::getColumn($this->getQueryData(), $this->userColumn));
			if (!empty($ids)) {
				$this->usersNames = ArrayHelper::shortNames(
					User::getSelectList($ids, false)
				);
			}
		}
		return $this->usersNames;
	}

	protected function getAreaChartId(): string {
		return $this->getId() . '.area-chart';
	}

	protected function getDonutUsersStatusesChartId(): string {
		return $this->getId() . '.donut-user-statuses-chart';
	}

}
