<?php

namespace common\widgets\charts;

use common\helpers\Html;
use Yii;
use yii\base\Widget;
use yii\helpers\Json;
use yii\web\JsExpression;

class ChartsWidget extends Widget {

	public static $autoIdPrefix = 'c';

	public const TYPE_AREA = 'area';

	public const TYPE_BAR = 'bar';
	public const TYPE_COLUMN = 'column';

	public const TYPE_RADIAL_BAR = 'radialBar';

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

	public bool $legendFormatterAsSeriesWithCount = false;
	public bool $legendFormatterAsSeriesAsCurrency = false;

	public bool $showDonutTotalLabels = false;

	public array $containerOptions = [];

	public static function radialDataFromCounts(array $data, int $precision = 1): array {
		$total = array_sum($data);
		$radialData = [];
		foreach ($data as $count) {
			$value = $count ? $count / $total * 100 : 0;
			$value = round($value, $precision);
			$radialData[] = $value;
		}
		return $radialData;
	}

	public function init() {
		if (!isset($this->chart['defaultLocale'])) {
			$this->chart['defaultLocale'] = Yii::$app->language;
		}
		if ($this->chart['defaultLocale'] === 'pl') {
			$this->chart['locales'][] = $this->getPlLanguageData();
		}
		if ($this->legendFormatterAsSeriesWithCount && !isset($this->options['legend']['formatter'])) {
			$this->options['legend']['formatter'] = $this->legendFormaterSeriesNameWithCount($this->legendFormatterAsSeriesAsCurrency);
		}
		if ($this->showDonutTotalLabels) {
			$this->options['plotOptions']['pie']['donut']['labels'] = $this->donutLabels();
		}
		parent::init();
	}

	public function run() {
		if (empty($this->series)) {
			return '';
		}
		ChartsAsset::register($this->getView());
		$options = $this->containerOptions;
		$options['id'] = $this->getId();
		$this->view->registerJs($this->getScript());

		return Html::tag('div', '', $options);
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
					'download' => 'Pobierz',
					"exportToCSV" => "Pobierz CSV",
					"exportToPNG" => "Pobierz PNG",
					"selection" => "Wybór",
					"selectionZoom" => "Zoom",
					"zoomIn" => "Zbliż",
					"zoomOut" => "Oddal",
					"pan" => "Przesuwanie",
					"reset" => "Reset Zoom",
				],
				'days' => [
					'Niedziela',
					'Poniedziałek',
					'Wtorek',
					'Środa',
					'Czwartek',
					'Piątek',
					'Sobota',
				],
				'shortDays' => [
					'Niedz.',
					'Pon.',
					'Wt.',
					'Śr.',
					'Czw.',
					'Pt.',
					'Sob.',
				],
				'months' => [
					'Styczeń',
					'Luty',
					'Marzec',
					'Kwiecień',
					'Maj',
					'Czerwiec',
					'Lipiec',
					'Sierpień',
					'Wrzesień',
					'Październik',
					'Listopad',
					'Grudzień',
				],
				'shortMonths' => [
					'Sty',
					'Lut',
					'Mar',
					'Kwi',
					'Maj',
					'Cze',
					'Lip',
					'Śie',
					'Wrz',
					'Paź',
					'Lis',
					'Gru',
				],
			],
		];
	}

	public static function currencyFormatterExpression(array $options = [], string $language = null, string $currencyCode = null): JsExpression {
		return new JsExpression("function (value) {
					  return valueToCurrencyFormat(value);
  					}"
		);
	}

	public function legendFormaterSeriesNameWithCount(bool $currency = false): JsExpression {
		return new JsExpression(
			'function(seriesName, opts){ 
			var count = opts.w.globals.series[opts.seriesIndex];
			if(' . Json::encode($currency) . '){
				count = valueToCurrencyFormat(count);
			}
			return [
			seriesName,
			 " - ",
			  count
			  ];}'
		);
	}

	protected function donutLabels(): array {
		return [
			'show' => true,
			'total' => [
				'show' => true,
				'showAlways' => true,
				'label' => Yii::t('common', 'Sum'),
			],
		];
	}

}
