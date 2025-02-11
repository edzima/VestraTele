<?php

namespace common\widgets\grid;

use common\assets\TooltipAsset;
use common\helpers\Html;
use Yii;

class DateTimeColumn extends DataColumn {

	public bool $tooltip = true;
	public $format = 'raw';
	public $noWrap = true;

	public function getDataCellValue($model, $key, $index) {
		$value = parent::getDataCellValue($model, $key, $index);
		return Html::tag('span',
			Yii::$app->formatter->asDate($value), [
				TooltipAsset::DEFAULT_ATTRIBUTE_NAME => Yii::$app->formatter->asTime($value),
				'class' => 'date-time-column-value',
			]);
	}

}
