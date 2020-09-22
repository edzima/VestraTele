<?php

namespace common\widgets\address;

use edzima\teryt\controllers\SimcController;
use edzima\teryt\models\Simc;
use kartik\select2\Select2;
use Yii;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 * Class CitySimcInputWidget
 * @todo move to edzima/yii2-teryt
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class CitySimcInputWidget extends Select2 {

	public string $cityProperty = 'city';
	public array $cityListRoute = ['/teryt/simc/city-list'];

	public function init() {
		if (!isset($this->options['placeholder'])) {
			$this->options['placeholder'] = Yii::t('address', 'Search for a city ...');
		}
		if (!isset($this->initValueText) && $this->model->{$this->attribute} !== null) {
			$city = $this->model->{$this->cityProperty};
			if ($city instanceof Simc) {
				$this->initValueText = $city->nameWithRegionAndDistrict;
			}
		}
		if (empty($this->pluginOptions)) {
			$this->pluginOptions = [
				'allowClear' => true,
				'minimumInputLength' => SimcController::MIN_QUERY_LENGTH,
				'language' => [
					'errorLoading' => new JsExpression("function () { return '"
						. Yii::t('address', 'Waiting for results...')
						. "'; }"),
				],
				'ajax' => [
					'url' => Url::to($this->cityListRoute),
					'dataType' => 'json',
					'data' => new JsExpression('function(params) { return {q:params.term}; }'),
				],
				'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
				'templateResult' => new JsExpression('function(city) { return city.text; }'),
				'templateSelection' => new JsExpression('function (city) { return city.text; }'),
			];
		}

		parent::init();
	}
}
