<?php

use backend\helpers\Html;
use backend\helpers\Url;
use backend\modules\issue\models\IssueStats;
use common\helpers\ArrayHelper;
use common\models\entityResponsible\EntityResponsible;
use common\models\issue\IssueStage;
use common\models\user\User;
use common\widgets\ChartsWidget;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model IssueStats */
/* @var string $widgetId */

$this->params['issueParentTypeNav'] = [
	'route' => ['/issue/stat/details', 'month' => $model->month, 'year' => $model->year],
];
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issues'), 'url' => ['issue/index']];

$this->title = Yii::t('backend', 'Stats - Details: {month}', [
	'month' => $model->getMonthName(),
]);

$series = [];
$data = [];

$agentsCount = $model->getAgentsCountForMonth();
$agentsData = [];
$paysData = [];

if (!empty($agentsCount)) {
	$agentsNames = User::getSelectList(array_keys($agentsCount), false, $model->db);
	foreach ($agentsCount as $id => $count) {
		$agentName = $agentsNames[$id];
		$data[] = [
			'x' => $agentsNames[$id],
			'y' => $count,
		];
		$agentsData['series'][] = (int) $count;
		$agentsData['labels'][] = $agentName;
	}

//	foreach ($model->getAgentPaysSumForMonth() as $userId => $sum) {
//		if ($sum) {
//			$paysData['all'][] = [
//				'x' => $agentsNames[$userId],
//				'y' => $sum,
//			];
//		}
//	}
//
//	foreach ($model->getAgentPaidPaySumForMonth() as $userId => $sum) {
//		$paysData['paid'][] = [
//			'x' => $agentsNames[$userId],
//			'y' => $sum,
//		];
//	}
//
//	foreach ($model->getAgentDelayedPaySumForMonth() as $userId => $sum) {
//		$paysData['delayed'][] = [
//			'x' => $agentsNames[$userId],
//			'y' => $sum,
//		];
//	}
}

$series[] = [
	'name' => 'Sprawy',
	'data' => $data,
];

$stagesData = [];
$stagesCount = $model->getStagesCountForMonth();
if (!empty($stagesCount)) {
	$stagesNames = ArrayHelper::map(
		IssueStage::find()
			->all($model->db),
		'id',
		'name');
	foreach ($stagesCount as $id => $count) {
		$stageName = $stagesNames[$id];
		$stagesData['series'][] = (int) $count;
		$stagesData['labels'][] = $stageName;
	}
}

$entityResponsibleData = [];
$entityCount = $model->getEntityResponsibleCountForMonth();
if (!empty($entityCount)) {
	$entityNames = ArrayHelper::map(
		EntityResponsible::find()
			->andWhere(['id' => array_keys($entityCount)])
			->asArray()
			->all($model->db),
		'id',
		'name'
	);
	foreach ($entityCount as $id => $count) {
		$entityName = $entityNames[$id];
		$entityResponsibleData['series'][] = (int) $count;
		$entityResponsibleData['labels'][] = $entityName;
	}
}

$modelId = $widgetId . '-' . $model->year . '-' . $model->month;

?>

<?= $this->render('_search', [
	'model' => $model,
]) ?>


<div class="issue-stats-details">
	<?= Yii::$app->request->isPjax ? Html::tag('h3', mb_strtoupper($model->getMonthName()), ['class' => 'text-center']) : '' ?>
	<div class="nav">
		<div class="pull-right">
			<?= Html::a('<',
				Url::current([
					Url::PARAM_ISSUE_PARENT_TYPE => $model->issueMainTypeId,
					'month' => $model->month > 1
						? $model->month - 1
						: 12,
					'year' => $model->month > 1
						? $model->year
						: $model->year - 1,
				]),
				[
					'data-pjax' => 1,
					'class' => 'btn btn-info',
				])
			?>

			<?= Html::a('>',

				Url::current([
					Url::PARAM_ISSUE_PARENT_TYPE => $model->issueMainTypeId,
					'month' => $model->month < 12
						? $model->month + 1
						: 1,
					'year' => $model->month < 12
						? $model->year
						: $model->year + 1,
				]), [
					'class' => 'btn btn-info',
				])
			?>
		</div>

	</div>

	<!--	--><?php //ChartsWidget::widget([
	//		'type' => 'bar',
	//		'id' => 'bar-pie-' . $modelId,
	//		'height' => '400',
	//		'series' => $series,
	//		'chartOptions' => [
	//			'xaxis' => [
	//				'type' => 'category',
	//			],
	//		],
	//
	//	]); ?>

	<div class="row">


		<div class="col-md-12">

			<?= !empty($agentsData) ?
				ChartsWidget::widget([
					'type' => 'donut',
					'id' => 'chart-agents-' . $modelId,
					'height' => 600,
					'series' => $agentsData['series'],
					'chartOptions' => [
						'labels' => $agentsData['labels'],
						'title' => [
							'text' => Yii::t('issue', 'Issues Count'),
							'align' => 'center',
						],
						'legend' => [
							//	'position' => 'bottom',
							'width' => 200,
							'formatter' => new JsExpression('function(seriesName, opts){
							return [seriesName, " - ", opts.w.globals.series[opts.seriesIndex]];
							}'),
						],
						'plotOptions' => [
							'pie' => [
								'donut' => [
									'labels' => [
										'show' => true,
										'total' => [
											'show' => true,
											'showAlways' => true,
											'label' => Yii::t('common', 'Sum'),
										],
									],
								],
							],
						],
					],
				])
				: ''
			?>
		</div>


		<div class="col-md-12">
			<?= !empty($paysData)
				? ChartsWidget::widget([
					'type' => 'bar',
					'id' => 'chart-pays-' . $modelId,
					'height' => 420,
					'series' => [
						[
							'name' => 'Suma',
							'data' => $paysData['all'],
						],
						[
							'name' => 'Opłacone',
							'data' => $paysData['paid'],
						],
						[
							'name' => 'Opóźnione',
							'data' => $paysData['delayed'],
						],

					],
					'chartOptions' => [
						'chart' => [
							'stacked' => true,
							'stackType' => '100%',
						],
						'plotOptions' => [
							'bar' => [
								'horizontal' => true,
							],
						],
						'title' => [
							'text' => Yii::t('settlement', 'Settlements'),
							'align' => 'center',
						],
						'xaxis' => [
							'type' => 'category',
						],
					],
				]) : '' ?>

		</div>

		<div class="col-md-6">
			<?= !empty($stagesData)
				? ChartsWidget::widget([
					'type' => 'pie',
					'id' => 'chart-stages-' . $modelId,
					'height' => 420,
					'series' => $stagesData['series'],
					'chartOptions' => [
						'labels' => $stagesData['labels'],
						'title' => [
							'text' => Yii::t('issue', 'Stages'),
							'align' => 'center',
						],
						'legend' => [
							'formatter' => new JsExpression('function(seriesName, opts){
							return [seriesName, " - ", opts.w.globals.series[opts.seriesIndex]];
							}'),
						],
					],
				])
				: ''
			?>

		</div>


		<div class="col-md-6">
			<?= !empty($entityResponsibleData) ?
				ChartsWidget::widget([
					'type' => 'pie',
					'id' => 'chart-entity-' . $modelId,
					'height' => 420,
					'series' => $entityResponsibleData['series'],
					'chartOptions' => [
						'labels' => $entityResponsibleData['labels'],
						'title' => [
							'text' => Yii::t('issue', 'Entity'),
							'align' => 'center',
						],
						'legend' => [
							'width' => 200,
							//		'position' => 'bottom',
							'formatter' => new JsExpression('function(seriesName, opts){
							return [seriesName, " - ", opts.w.globals.series[opts.seriesIndex]];
							}'),
						],
					],
				])
				: '' ?>

		</div>
	</div>
</div>

