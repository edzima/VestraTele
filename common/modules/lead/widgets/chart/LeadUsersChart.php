<?php

namespace common\modules\lead\widgets\chart;

use common\helpers\ArrayHelper;
use common\models\user\User;
use common\modules\lead\models\LeadUser;
use common\widgets\charts\ChartsWidget;
use Yii;
use yii\base\Widget;
use yii\db\ActiveQuery;

class LeadUsersChart extends Widget {

	public bool $stacked = true;

	public string $height = '480px';

	public array $series = [];

	public ActiveQuery $query;

	public ?array $queryData = null;

	public string $userColumn = 'user_id';

	public string $countColumn = 'count';

	public ?string $emptyUserName = null;
	public ?string $notfoundUserName = null;

	public array $usersNames = [];

	public ?string $totalTitle = null;

	public ?string $areaGroup = null;

	public array $userTypes = [
		LeadUser::TYPE_OWNER,
	];
	public array $navChartOptions = [];

	public function init(): void {
		parent::init();
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
		return $this->render('lead-user-chart', [
			'nav' => $renderNavAndTotal ? $this->renderNav() : '',
			'donutUsersChart' => $renderNavAndTotal ? $this->renderDonutUsersChart() : '',
			'areaUsersStatusChart' => $this->renderUsersStatusChart(),
		]);
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

	protected function renderNav(): string {
		$options = $this->navChartOptions;
		$options = array_merge($options, [
			'series' => $this->getSeries(),
			'chartID' => $this->getDonutChartId(),
			'chartToggleId' => $this->getAreaChartId(),
		]);
		return NavChart::widget($options);
	}

	public function renderDonutUsersChart(): string {
		$labels = $this->getLabels();
		if (empty($labels)) {
			return '';
		}
		$total = $this->getTotalData();
		return ChartsWidget::widget([
			'type' => ChartsWidget::TYPE_DONUT,
			'series' => array_values($total),
			'height' => $this->height,
			'showDonutTotalLabels' => true,
			'legendFormatterAsSeriesWithCount' => true,
			'chart' => [
				'id' => $this->getDonutChartId(),
			],
			'options' => [
				'labels' => $this->getLabels(),
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
			return $this->renderSingleUserChart();
		}
		return $this->renderMultipleUsersChart();
	}

	protected function renderSingleUserChart(int $index = 0): string {
		$series = $this->getSeries();
		$colors = ArrayHelper::getColumn($series, 'color', []);
		$labels = ArrayHelper::getColumn($series, 'name', []);
		$seriesArrayData = ArrayHelper::getColumn($series, 'data', []);
		$data = [];
		foreach ($seriesArrayData as $seriesData) {
			$data[] = $seriesData[$index];
		}
		$title = $this->totalTitle . ' - ' . $this->getUserName(array_keys($this->getTotalData())[$index]);
		return ChartsWidget::widget([
			'type' => ChartsWidget::TYPE_DONUT,
			'series' => $data,
			'options' => [
				'labels' => $labels,
				'colors' => $colors,
				'title' => [
					'text' => $title,
					'align' => 'center',
				],
			],
			'height' => '400px',
			'legendFormatterAsSeriesWithCount' => true,
			'showDonutTotalLabels' => true,
		]);
	}

	protected function renderMultipleUsersChart(): string {
		$series = array_values($this->getSeries());
		$labels = $this->getLabels();
		return ChartsWidget::widget([
			'type' => ChartsWidget::TYPE_AREA,
			'height' => $this->height,
			'series' => $series,
			'chart' => [
				'stacked' => $this->stacked,
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

	protected function getLabels(): array {
		$ids = array_keys($this->getTotalData());
		return $this->getUsersNamesFromIds($ids);
	}

	protected function getSeries(): array {
		return $this->series;
	}

	protected function getTotalData(): array {
		return [];
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
		return $this->getId() . '-area-chart';
	}

	protected function getDonutChartId(): string {
		return $this->getId() . '-donut-user-statuses-chart';
	}

}
