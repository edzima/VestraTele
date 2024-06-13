<?php

namespace common\widgets\charts;

use common\helpers\Html;
use Yii;
use yii\base\Widget;
use yii\helpers\Json;
use yii\web\JsExpression;

class ChartsWidget extends Widget {

	public static $autoIdPrefix = 'c';

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

	public array $containerOptions = [];

	public function init() {
		if (!isset($this->chart['defaultLocale'])) {
			$this->chart['defaultLocale'] = Yii::$app->language;
		}
		if ($this->chart['defaultLocale'] === 'pl') {
			$this->chart['locales'][] = $this->getPlLanguageData();
		}
		if ($this->legendFormatterAsSeriesWithCount && !isset($this->options['legend']['formatter'])) {
			$this->options['legend']['formatter'] = static::legendFormaterSeriesNameWithCount();
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
		if ($language === null) {
			$language = Yii::$app->formatter->language;
		}
		if ($currencyCode === null) {
			$currencyCode = Yii::$app->formatter->currencyCode;
		}
		$options['style'] = 'currency';
		$options['currency'] = $currencyCode;
		if (!isset($options['maximumFractionDigits'])) {
			$options['maximumFractionDigits'] = 0;
		}
		$options = Json::encode($options);
		return new JsExpression("function (value) {
					  return new Intl.NumberFormat('$language',$options)
					  		.format(value);
  					}"
		);
	}

	public static function legendFormaterSeriesNameWithCount(): JsExpression {
		return new JsExpression(
			'function(seriesName, opts){ return [seriesName, " - ", opts.w.globals.series[opts.seriesIndex]];}'
		);
	}

}
