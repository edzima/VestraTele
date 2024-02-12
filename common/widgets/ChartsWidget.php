<?php

namespace common\widgets;

use onmotion\apexcharts\ApexchartsWidget;
use Yii;
use yii\helpers\Json;
use yii\web\JsExpression;

class ChartsWidget extends ApexchartsWidget {

	public static $autoIdPrefix = 'ac';

	public function init() {
		if (!isset($this->chartOptions['defaultLocale'])) {
			$this->chartOptions['defaultLocale'] = Yii::$app->language;
		}
		if ($this->chartOptions['defaultLocale'] === 'pl') {
			$this->chartOptions['locales'][] = $this->getPlLanguageData();
		}
		parent::init();
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

	public function getId($autoGenerate = true) {
		if ($this->id === 'apexcharts-widget') {
			return parent::getId($autoGenerate);
		}
		return $this->id;
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
}
