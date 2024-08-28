<?php

use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\searches\LeadChartReportSearch;
use common\modules\lead\widgets\chart\LeadReportStatusChart;
use common\modules\lead\widgets\chart\LeadStatusChart;
use common\modules\lead\widgets\chart\LeadTypeChart;

/* @var $this yii\web\View */
/* @var $searchModel LeadChartReportSearch */

$this->title = Yii::t('lead', 'Lead Reports Statistics');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Reports'), 'url' => ['/lead/report/index']];
$this->params['breadcrumbs'][] = Yii::t('lead', 'Statistics');

//echo count($searchModel->getLeadUserTimeStats());

$time = $searchModel->getLeadUserTimeStats();
$series = [
	'min' => [
		'name' => 'Min',
		'data' => [],
	],
	'max' => [
		'name' => 'Max',
		'data' => [],
	],
	'avg' => [
		'name' => 'AVG',
		'data' => [],
	],
];
$labels = [];
$usersNames = $searchModel->getOwnersNames();
foreach ($time as $ownerId => $data) {
	$labels[] = $usersNames[$ownerId];
}

?>

<div class="lead-report-chart">
	<?= $this->render('_chart-search', [
		'model' => $searchModel,
	]) ?>


	<?php if ($searchModel->getBaseQuery()->count()): ?>

		<div class="row">

			<div class="col-sm-12 col-md-6 col-lg-4">
				<?= LeadStatusChart::widget([
					'grouping' => $searchModel->groupLeadStatus,
					'statusColor' => $searchModel->getLeadStatusColor(),
					'statusesList' => $searchModel
						->getBaseQuery()
						->select(LeadReport::tableName() . '.old_status_id')
						->column(),
					'chartOptions' => [
						'height' => 380,
						'legendFormatterAsSeriesWithCount' => true,
						'options' => [
							'title' => [
								'text' => Yii::t('lead', 'Old Status'),
								'align' => 'center',

							],
						],
					],
				]) ?>
			</div>


			<div class="col-sm-12 col-md-6 col-lg-4">
				<?= LeadStatusChart::widget([
					'grouping' => $searchModel->groupLeadStatus,
					'statusColor' => $searchModel->getLeadStatusColor(),
					'statusesList' => $searchModel
						->getBaseQuery()
						->select(LeadReport::tableName() . '.status_id')
						->column(),
					'chartOptions' => [
						'height' => 380,
						'showDonutTotalLabels' => true,
						'legendFormatterAsSeriesWithCount' => true,
						'options' => [
							'title' => [
								'text' => Yii::t('lead', 'Report Status'),
								'align' => 'center',

							],
						],
					],
				]) ?>
			</div>


			<div class="col-sm-12 col-md-6 col-lg-4">
				<?= LeadStatusChart::widget([
					'grouping' => $searchModel->groupLeadStatus,
					'statusColor' => $searchModel->getLeadStatusColor(),
					'statusesList' => $searchModel
						->getBaseQuery()
						->joinWith('lead', false)
						->select(Lead::tableName() . '.status_id')
						->column(),
					'chartOptions' => [
						'height' => 380,
						'legendFormatterAsSeriesWithCount' => true,
						'options' => [
							'title' => [
								'text' => Yii::t('lead', 'Current Status'),
								'align' => 'center',
							],
						],
					],
				]) ?>
			</div>
		</div>

		<div class="current-status">
			<h4>
				<?= Yii::t('lead', 'Current Status') ?>
			</h4>

			<?= LeadReportStatusChart::widget([
				'query' => $searchModel->getBaseQuery(),
				'statusType' => LeadReportStatusChart::STATUS_LEAD_STATUS,
				'statusColor' => $searchModel->getLeadStatusColor(),
				'groupStatus' => $searchModel->groupLeadStatus,
				'areaGroup' => 'reportOwners',
			]) ?>

		</div>

		<div class="new-status">

			<h4>
				<?= Yii::t('lead', 'Report Status') ?>
			</h4>

			<?= LeadReportStatusChart::widget([
				'query' => $searchModel->getBaseQuery(),
				'statusType' => LeadReportStatusChart::STATUS_REPORT_STATUS_NEW,
				'statusColor' => $searchModel->getLeadStatusColor(),
				'groupStatus' => $searchModel->groupLeadStatus,
				'areaGroup' => 'reportOwners',
			]) ?>
		</div>

		<div class="old-status">
			<h4>
				<?= Yii::t('lead', 'Old Status') ?>
			</h4>

			<?= LeadReportStatusChart::widget([
				'query' => $searchModel->getBaseQuery(),
				'statusType' => LeadReportStatusChart::STATUS_REPORT_STATUS_OLD,
				'statusColor' => $searchModel->getLeadStatusColor(),
				'groupStatus' => $searchModel->groupLeadStatus,
				'areaGroup' => 'reportOwners',
			]) ?>

		</div>

		<div class="row">
			<div class="col-sm-12 col-md-6 col-lg-4">
				<?= LeadTypeChart::widget([
					'typesCount' => $searchModel->getLeadTypesCount(),
				]) ?>
			</div>
		</div>

	<?php endif; ?>

</div>
