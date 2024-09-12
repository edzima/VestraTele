<?php

use common\helpers\ArrayHelper;
use common\widgets\charts\ChartsWidget;

/* @var $chartCostDayData array */
?>


<?= !empty($chartCostDayData['series'])
	? ChartsWidget::widget([
		'series' => $chartCostDayData['series'],
		'type' => ChartsWidget::TYPE_AREA,
		'height' => '400px',
		'chart' => ['stacked' => true,],
		'options' => [
			'xaxis' => ['type' => 'datetime',],
			'yaxis' => [
				[
					'seriesName' => $chartCostDayData['yAxis']['seriesNames.leads'],
					'decimalsInFloat' => 0,
					'showForNullSeries' => false,
					'title' => ['text' => Yii::t('lead', 'Leads'),],
				],
				[
					'seriesName' => $chartCostDayData['yAxis']['seriesNames.cost'],
					'decimalsInFloat' => 1,
					'showForNullSeries' => false,
					'title' => ['text' => Yii::t('lead', 'Cost'),],
				],
				[
					'seriesName' => $chartCostDayData['yAxis']['seriesNames.avg'],
					'opposite' => true,
					'showForNullSeries' => false,
					'decimalsInFloat' => 1,
					'title' => ['text' => Yii::t('lead', 'Single Lead cost Value'),],
				],
			],
			'stroke' => [
				'curve' => 'smooth',
				'width' => ArrayHelper::getColumn($chartCostDayData['series'], 'strokeWidth', false),
			],
		],
	])
	: ''
?>
