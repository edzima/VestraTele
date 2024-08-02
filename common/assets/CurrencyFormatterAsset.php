<?php

namespace common\assets;

use Yii;
use yii\helpers\Json;
use yii\web\AssetBundle;
use yii\web\JsExpression;

class CurrencyFormatterAsset extends AssetBundle {

	public string $functionName = 'valueToCurrencyFormat';

	public array $options = [];

	public function registerAssetFiles($view) {
		parent::registerAssetFiles($view);
		$view->registerJs($this->script($this->options));
	}

	protected function script(array $options = [], string $language = null, string $currencyCode = null) {
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
		return new JsExpression("function {$this->functionName}(value) {
					  return new Intl.NumberFormat('$language',$options)
					  		.format(value);
  					}");
	}

}
