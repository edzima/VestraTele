<?php

namespace common\widgets\charts;

use common\helpers\Html;
use Yii;
use yii\base\Widget;
use yii\helpers\Json;

class ChartsWidget extends Widget {

	public static $autoIdPrefix = 'c';

	public const TYPE_BAR = 'bar';

	public const TYPE_PIE = 'pie';
	public const TYPE_LINE = 'line';
	public const TYPE_DONUT = 'donut';
	public const TYPE_DEFAULT = self::TYPE_LINE;

	public string $type = self::TYPE_DEFAULT;

	public array $chart = [];
	public array $series = [];
	public array $options = [];

	public string $height = 'auto';

	public int $timeout = 50;

	public array $containerOptions = [];

	public function init() {
		if (!isset($this->options['defaultLocale'])) {
			$this->options['defaultLocale'] = Yii::$app->language;
		}
		if ($this->options['defaultLocale'] === 'pl') {
			$this->options['locales'][] = $this->getPlLanguageData();
		}
		parent::init();
	}

	public function run() {
		ChartsAsset::register($this->getView());
		$options = $this->containerOptions;
		$options['id'] = $this->getId();
		echo Html::tag('div', '', $options);

		$this->view->registerJs($this->getScript());
	}

	public function getScript(): string {
		$timeout = $this->timeout;
		$id = $this->getId();
		$options = Json::encode($this->getOptions());
		$script = <<<JS
	setTimeout(function() {
				const chart = (new ApexCharts(document.querySelector("#{$id}"),{$options}))
				chart.render();
			}, {$timeout})
JS;

		return $script;
	}

	public function getOptions(): array {
		$options = [
			'chart' => $this->getChartOptions(),
			'series' => $this->series,
		];
		return array_merge($options, $this->options);
	}

	public function getChartOptions(): array {
		$options = $this->chart;
		$options['type'] = $this->type;
		$options['height'] = $this->height;
		return $options;
	}

	protected function getPlLanguageData(): array {
		return [
			'name' => 'pl',
			'options' => [
				'toolbar' => [
					'exportToSVG' => 'Pobierz SVG',
				],
			],
		];
	}

}
