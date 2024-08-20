<?php

use common\modules\lead\models\searches\LeadChartSearch;
use common\widgets\charts\ChartsWidget;

/* @var $model LeadChartSearch */
/* @var $chartContainerOptions array */

$hoursData = $model->getLeadsGroupsByHours();

$hoursChartData = [];
foreach ($hoursData as $data) {
	$hour = $data['hour'];
	if (!isset($hoursChartData['categories'][$hour])) {
		$hoursChartData['categories'][$hour] = $hour;
	}
	$provider = $data['provider'];
	if (empty($provider)) {
		$provider = Yii::t('lead', 'Without Provider');
	} else {
		$provider = LeadChartSearch::getProvidersNames()[$provider];
	}
	if (!isset($hoursChartData['series'][$provider])) {
		$hoursChartData['series'][$provider] = [
			'name' => $provider,
			'data' => [],
		];
	}
	$hoursChartData['series'][$provider]['data'][] = [
		'x' => (int) ($hour),
		'y' => (int) ($data['count']),
	];
}

?>

<?= !empty($hoursChartData)
	? ChartsWidget::widget([
		'containerOptions' => $chartContainerOptions,
		'height' => '350px',
		'type' => ChartsWidget::TYPE_BAR,
		'series' => array_values($hoursChartData['series']),
		'options' => [
			'xaxis' => [
				'type' => 'category',
			],
			'dataLabels' => [
				'enabled' => false,
			],
			'title' => [
				'text' => Yii::t('lead', 'Hours'),
				'align' => 'center',
			],
		],
	])
	: ''
?>
