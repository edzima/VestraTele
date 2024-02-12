<?php

use backend\helpers\Html;
use backend\modules\issue\models\HostIssueStats;
use common\helpers\Inflector;
use common\widgets\ChartsWidget;

/* @var $this yii\web\View */
/* @var $models HostIssueStats[] */

$categories = [];
$series = [];
foreach ($models as $model) {
	$stats = $model->getStats();
	$paySum = (int) $stats->getPaysSum();
	$costsSum = (int) $stats->getCostsSum();
	$paidSum = (int) $stats->getPayidPaysSum();
	$provisionsSum = (int) $stats->getProvisionsSum();
	$series['pay']['name'] = 'Total Pays';
	$series['pay-paid']['name'] = 'Paid Pays';
	$series['costs']['name'] = 'Costs';
	$series['provisions']['name'] = 'Provisions';

	$series['costs']['data'][] = $costsSum;
	$series['pay']['data'][] = $paySum;
	$series['pay-paid']['data'][] = $paidSum;
	$series['provisions']['data'][] = $provisionsSum;

	$data[] = $paySum;
	$categories[] = $model->getHostName();
}

echo ChartsWidget::widget([
	'type' => 'bar',
	'id' => 'chart-bar-pays',
	'height' => 420,
	'series' => array_values($series),
	'chartOptions' => [
		'xaxis' => [
			'categories' => $categories,
		],
		'yaxis' => [
			'labels' => [
				'formatter' => ChartsWidget::currencyFormatterExpression(),
			],
		],
		'plotOptions' => [
			'bar' => [
				'dataLabels' => [
					'position' => 'top',
				],
			],
		],
		'dataLabels' => [
			'enabled' => true,
			'formatter' => ChartsWidget::currencyFormatterExpression(),
			'offsetY' => -20,
			'style' => [
				'colors' => ['#3f3f3f'],
			],
		],
	],
]);

echo ChartsWidget::widget([
	'type' => 'pie',
	'id' => 'chart-pays',
	'height' => 420,
	'series' => $data,
	//'series' => $series,
	'chartOptions' => [
		'labels' => $categories,
		'title' => [
			'text' => Yii::t('issue', 'Pays'),
			'align' => 'center',
		],
	],
])
?>

<?php foreach ($models as $model): ?>
	<?= Html::tag('h2', $model->getHostName() . ' :' . $model->getStats()->getAllCount()); ?>

	<?= $this->render('details', [
			'model' => $model->getStats(),
			'widgetId' => Inflector::slug($model->getHostName()),
		]
	) ?>

<?php endforeach; ?>
