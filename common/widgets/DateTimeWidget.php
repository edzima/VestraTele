<?php

namespace common\widgets;

use trntv\yii\datetime\assets\DateTimeAsset;
use trntv\yii\datetime\DateTimeWidget as BaseDateTimeWidget;
use yii\helpers\Json;

class DateTimeWidget extends BaseDateTimeWidget {

	public $phpDatetimeFormat = 'yyyy-MM-dd HH:mm';

	public $clientOptions = [
		'allowInputToggle' => true,
		'sideBySide' => true,
		'widgetPositioning' => [
			'horizontal' => 'auto',
			'vertical' => 'auto',
		],
	];

	/**
	 * @var array
	 */
	protected $defaultPhpMomentMapping = [
		'yyyy-MM-dd' => 'YYYY-MM-DD',            // 2014-05-14
		'yyyy-MM-dd HH:mm' => 'YYYY-MM-DD HH:mm', //2014-05-14 13:55
		'yyyy-MM-dd HH:mm:ss' => 'YYYY-MM-DD HH:mm:ss', //2014-05-14 13:55:15
	];

	protected function registerJs() {
		DateTimeAsset::register($this->getView());
		$clientOptions = Json::encode($this->clientOptions);
		$this->getView()->registerJs("$('#{$this->containerOptions['id']}').datetimepicker({$clientOptions});");

		if (!empty($this->clientEvents)) {
			$js = [];
			foreach ($this->clientEvents as $event => $handler) {
				$js[] = "jQuery('#{$this->containerOptions['id']}').on('$event', $handler);";
			}
			$this->getView()->registerJs(implode("\n", $js));
		}
	}

}
