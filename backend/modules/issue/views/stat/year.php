<?php

use backend\helpers\Html;
use backend\helpers\Url;
use backend\modules\issue\models\IssueStats;
use common\models\user\User;
use onmotion\apexcharts\ApexchartsWidget;

/* @var $this yii\web\View */
/* @var $model IssueStats */

$this->params['issueParentTypeNav'] = [
	'route' => ['/issue/stat/year', 'year' => $model->year],
];
$this->title = Yii::t('backend', 'Stats - Details: {year}', [
	'year' => $model->year,
]);

$series = [];
$models = $model->getMonthModels(SORT_ASC);
foreach ($models as $model) {
	$agentsCount = $model->getAgentsCountForMonth();

	foreach ($agentsCount as $id => $count) {
		$series[$id]['data'][] = [
			'x' => $model->getMonthName(),
			'x' => date("{$model->year}-{$model->month}-16"),
			'y' => (int) $count,
		];
	}
}
$usersIds = array_keys($series);
$usersNames = User::getSelectList($usersIds, false);
foreach ($series as $userId => &$data) {
	$data['name'] = $usersNames[$userId];
}

?>

<div class="issue-stats-details">

	<?= Html::a($model->year - 1,
		[
			'year',
			'year' => $model->year - 1,
			Url::PARAM_ISSUE_PARENT_TYPE => $model->issueMainTypeId,

		], [
			'class' => 'btn btn-info',
		])
	?>

	<?= Html::a($model->year + 1,
		[
			'year',
			'year' => $model->year + 1,
			Url::PARAM_ISSUE_PARENT_TYPE => $model->issueMainTypeId,
		], [
			'class' => 'btn btn-info',
		])
	?>

	<?= ApexchartsWidget::widget([
		'type' => 'bar',
		'height' => '500',
		'series' => array_values($series),
		'chartOptions' => [
			'xaxis' => [
				'type' => 'datetime',
			],

		],
	]) ?>

</div>

