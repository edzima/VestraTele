<?php

namespace common\modules\lead\widgets\chart;

use common\helpers\ArrayHelper;
use common\helpers\Html;
use Yii;
use yii\base\Widget;
use yii\bootstrap\Nav;

class NavChart extends Widget {

	public string $chartID;

	public array $series = [];

	public array $navOptions = [
		'options' => [
			'class' => 'nav-pills nav-scrollable',
		],
	];

	public array $itemOptions = [
		'linkOptions' => [
			'class' => 'btn btn-sm text-uppercase',
			'style' => [
				'color' => 'white',
			],
		],
	];

	public bool $labelWithCount = true;

	public bool $emptyValueAsZero = true;
	public ?string $chartToggleId = null;

	public function init(): void {
		parent::init();
		$this->itemOptions['linkOptions']['data-chart-id'] = $this->chartID;
	}

	public function run(): string {
		$navOptions = $this->navOptions;
		if (!isset($navOptions['items'])) {
			$items = $this->getItems();
			if (empty($items)) {
				return '';
			}
			$navOptions['items'] = $items;
			$navOptions['encodeLabels'] = false;
			Html::addCssClass($navOptions['options'], 'nav-chart');
		}
		return Nav::widget($navOptions);
	}

	private function getItems(): array {
		$items = [];
		foreach ($this->series as $series) {
			$items[] = $this->getItemConfig($series);
		}
		return $items;
	}

	protected function getItemConfig(array $series): array {
		$label = $this->getItemLabel($series);
		$serieOptions = ArrayHelper::remove($series, 'linkItemOptions', []);
		$options = array_merge($serieOptions, $this->itemOptions);
		$options['label'] = $label;
		$options['linkOptions']['onclick'] = 'event.preventDefault();onClickNavChart(this);';
		$options['linkOptions']['data-series-data'] = $this->getSeriesData($series);
		$options['linkOptions']['data-series-name'] = $series['name'];
		if ($this->chartToggleId) {
			$options['linkOptions']['data-chart-toggle'] = $this->chartToggleId;
		}
		if (isset($series['color'])) {
			Html::addCssStyle($options['linkOptions'], [
				'background-color' => $series['color'],
			]);
		}

		return $options;
	}

	protected function getItemLabel(array $series): string {
		$name = Html::encode($series['name']);
		return $name . $this->getItemBadge($series);
	}

	protected function getItemBadge(array $series): string {
		if (isset($series['withoutCount']) || !$this->labelWithCount) {
			return '';
		}
		$count = array_sum($series['data']);

		$count = round($count);
		if (isset($series['currencyFormatter'])) {
			$count = Yii::$app->formatter->asCurrency($count);
		}
		return Html::tag('span', $count, ['class' => 'badge']);
	}

	private function getSeriesData(array $series): array {
		$data = $series['data'];
		if (!$this->emptyValueAsZero) {
			return $data;
		}
		$emptyAsZero = [];
		foreach ($data as $value) {
			if (empty($value)) {
				$value = 0;
			}
			$emptyAsZero[] = $value;
		}
		return $emptyAsZero;
	}
}
