<?php

namespace common\widgets\grid;

use common\assets\TooltipAsset;
use common\helpers\Html;
use Yii;

class DateDurationColumn extends DataColumn {

	public bool $time = false;
	public bool $tooltip = true;
	public $format = 'raw';

	public function getDataCellValue($model, $key, $index) {
		$value = parent::getDataCellValue($model, $key, $index);
		return Html::tag('span',
			Yii::$app->formatter->asRelativeTime($value), [
				TooltipAsset::DEFAULT_ATTRIBUTE_NAME => $this->time
					? Yii::$app->formatter->asDatetime($value)
					: Yii::$app->formatter->asDate($value),
				'class' => 'date-time-column-value',
			]);
	}

}
