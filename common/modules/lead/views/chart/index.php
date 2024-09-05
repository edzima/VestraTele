<?php

use common\helpers\ArrayHelper;
use common\models\user\User;
use common\modules\lead\components\cost\CampaignCost;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\searches\LeadChartSearch;
use common\modules\lead\widgets\chart\LeadTypeChart;
use common\modules\lead\widgets\chart\LeadUsersCostsChart;
use common\modules\lead\widgets\chart\LeadUsersStatusChart;
use common\widgets\charts\ChartsWidget;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $searchModel LeadChartSearch */
/* @var $campaignsCost CampaignCost[] */

$this->title = Yii::t('lead', 'Leads');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = $this->title;

$leadStatusColor = $searchModel->getLeadStatusColor();
$leadsDaysData = $searchModel->getLeadsByDays();

$leadsCountSeries = [];
$leadDates = array_values(array_unique(ArrayHelper::getColumn($leadsDaysData, 'date')));
$hasGroup = false;

foreach ($leadsDaysData as $data) {
	$statusId = $data['status_id'];

	if (!isset($leadsCountSeries[$statusId])) {
		$statusModel = LeadStatus::getModels()[$statusId];
		if ($searchModel->groupedStatus === LeadChartSearch::STATUS_GROUP_ONLY_ASSIGNED) {
			if (empty($statusModel->chart_group)) {
				continue;
			}
		}
		$leadsCountSeries[$statusId] = [
			'name' => $statusModel->name,
			'data' => [],
		];
		if (!empty($statusModel->chart_group)) {
			$leadsCountSeries[$statusId]['group'] = $statusModel->chart_group;
			$hasGroup = true;
		}

		$leadsDaysData['colors'][] = $leadStatusColor->getStatusColor($statusModel);
	}

	//datetime
	//	$leadsCountSeries[$statusId]['data'][] = [
	//		$data['date'],
	//		(int) $data['count'],
	//	];
	//category
	$leadsCountSeries[$statusId]['data'][$data['date']] = (int) $data['count'];

	$leadsDaysData['labels'][$data['date']] = $data['date'];
}

foreach ($leadsCountSeries as $index => $data) {
	if ($hasGroup && !isset($data['group'])) {
		$leadsCountSeries[$index]['group'] = Yii::t('lead', 'Without group');
	}
	$data = $data['data'];
	$dataWithoutStatues = [];
	foreach ($leadDates as $date) {
		$dataWithoutStatues[] = isset($data[$date]) ? $data[$date] : 0;
	}

	$leadsCountSeries[$index]['data'] = $dataWithoutStatues;
}

$statusesCount = $searchModel->getLeadStatusesCount();
$statusData = [];
if ($searchModel->groupedStatus === LeadChartSearch::STATUS_GROUP_DISABLE) {
	foreach ($statusesCount as $status_id => $count) {
		$status = LeadStatus::getModels()[$status_id];
		$statusData['series'][] = $count;
		$statusData['labels'][] = $status->name;
		$statusData['colors'][] = $leadStatusColor->getStatusColor($status);
	}
}

if ($searchModel->groupedStatus !== LeadChartSearch::STATUS_GROUP_DISABLE) {
	$statusGroupData = [];
	$withoutGroup = $searchModel->groupedStatus === LeadChartSearch::STATUS_GROUP_WITHOUT_ASSIGNED;
	foreach ($statusesCount as $status_id => $count) {
		$status = LeadStatus::getModels()[$status_id];
		if (!$withoutGroup) {
			if (empty($status->chart_group)) {
				continue;
			}
		}
		$group = $status->chart_group ? $status->chart_group : Yii::t('lead', 'Without group');
		if (!isset($statusGroupData['series'][$group])) {
			$statusGroupData['series'][$group] = 0;
		}

		$statusGroupData['series'][$group] += $count;
		$statusGroupData['labels'][$group] = $group;
		$statusGroupData['colors'][$group] = $leadStatusColor->getStatusColor($status);
		if (!isset($statusGroupData['totalCount'])) {
			$statusGroupData['totalCount'] = 0;
		}
		$statusGroupData['totalCount'] += $count;
	}

	$statusGroupDataPercent = [];
	$statusGroupTotalCount = $statusGroupData['totalCount'] ?? 0;
	if ($statusGroupTotalCount) {
		foreach ($statusGroupData['series'] as $index => $data) {
			$statusGroupDataPercent[] = round($data / $statusGroupTotalCount * 100, 1);
		}
	}
}

$sourcesData = $searchModel->getLeadSourcesCount();
foreach ($sourcesData as $id => $count) {
	$source = LeadSource::getModels()[$id];
	$sourcesData['series'][] = $count;
	$sourcesData['labels'][] = $source->name;
}

$providersData = $searchModel->getLeadProvidersCount();
if (count($providersData) > 1) {
	foreach ($providersData as $provider => $count) {
		if (empty($provider)) {
			$name = Yii::t('lead', 'Without Provider');
		} else {
			$name = LeadChartSearch::getProvidersNames()[$provider];
		}
		$providersData['series'][] = $count;
		$providersData['labels'][] = $name;
	}
}

?>
<div class="lead-chart-index">
	<?= $this->render('_search', [
		'model' => $searchModel,
		'sourcesNames' => $sourcesData['names'] ?? [],
	]) ?>
	<div class="lead-charts">

		<?= LeadUsersStatusChart::widget([
			'query' => $searchModel->getBaseQuery(),
		]) ?>

		<?= LeadUsersCostsChart::widget([
			'query' => $searchModel->getBaseQuery(),
		]) ?>


		<div class="row">
			<div class="col-md-12">

				<?= Yii::$app->user->can(
					User::PERMISSION_LEAD_COST
				) ? $this->render('_campaign-cost', [
					'model' => $searchModel,
					'data' => $campaignsCost,
				])
					: ''
				?>
			</div>
		</div>


		<div class="row">
			<?= !empty($statusGroupData) && $searchModel->groupedStatusChartType === ChartsWidget::TYPE_RADIAL_BAR ?
				ChartsWidget::widget([
					'type' => ChartsWidget::TYPE_RADIAL_BAR,
					'containerOptions' => [
						'class' => 'col-sm-12 col-md-6 col-lg-4 status-charts',
						'style' => ['height' => '50vh',],
					],
					'id' => 'chart-leads-statuses-group-radial-count' . $searchModel->getUniqueId(),
					'series' => $statusGroupDataPercent,
					'showDonutTotalLabels' => true,
					'options' => [
						'colors' => array_values($statusGroupData['colors']),
						'labels' => array_values($statusGroupData['labels']),
						'title' => [
							'text' => Yii::t('lead', 'Status Count'),
							'align' => 'center',
						],
						'legend' => [
							'position' => 'bottom',
							'height' => '55',
						],
						'plotOptions' => [
							'radialBar' => [
								'track' => ['show' => true,],
								'inverseOrder' => true,
								'dataLabels' => [
									'show' => true,
									'name' => ['show' => true,],
									'value' => [
										'show' => true,
										'formatter' => new JsExpression("function (val) {
                    return val + '%';
                  }"),
									],
									'total' => [
										'show' => true,
										'label' => Yii::t('common', 'Sum'),
										'formatter' => new JsExpression("function (w) {
                    return " . $statusGroupTotalCount . "
                  }"),
									],
								],
							],
						],
					],
				])
				: ''
			?>

			<?= !empty($statusGroupData) && $searchModel->groupedStatusChartType === ChartsWidget::TYPE_DONUT ?
				ChartsWidget::widget([
					'type' => ChartsWidget::TYPE_DONUT,
					'containerOptions' => [
						'class' => 'col-sm-12 col-md-6 col-lg-4 status-charts',
						'style' => ['height' => '50vh',],
					],
					'id' => 'chart-leads-statuses-group-count' . $searchModel->getUniqueId(),
					'legendFormatterAsSeriesWithCount' => true,
					'showDonutTotalLabels' => true,
					'series' => array_values($statusGroupData['series']),
					'options' => [
						'colors' => array_values($statusGroupData['colors']),
						'labels' => array_values($statusGroupData['labels']),
						'title' => [
							'text' => Yii::t('lead', 'Status Count'),
							'align' => 'center',
						],
						'legend' => [
							'position' => 'bottom',
							//'width' => 200,
							'height' => '55',
						],
					],
				])
				: ''
			?>
			<?= !empty($statusData) ?
				ChartsWidget::widget([
					'type' => 'donut',
					'containerOptions' => [
						'class' => 'col-sm-12 col-md-6 col-lg-4 status-charts',
						'style' => ['height' => '50vh',],
					],
					'id' => 'chart-leads-statuses-count' . $searchModel->getUniqueId(),
					'legendFormatterAsSeriesWithCount' => true,
					'showDonutTotalLabels' => true,
					'series' => $statusData['series'],
					'options' => [
						'colors' => $statusData['colors'],
						'labels' => $statusData['labels'],
						'title' => [
							'text' => Yii::t('lead', 'Status Count'),
							'align' => 'center',
						],
						'legend' => [
							'position' => 'bottom',
							//'width' => 200,
							'height' => '55',
						],
					],
				])
				: ''
			?>


			<?= LeadTypeChart::widget([
				'typesCount' => $searchModel->getLeadTypesCount(),
				'containerOptions' => [
					'class' => 'col-sm-12 col-md-6 col-lg-4',
					'style' => ['height' => '50vh',],
				],
				'options' => [
					'legend' => [
						'position' => 'bottom',
						'height' => '55',
					],
				],
			])
			?>




			<?= !empty($sourcesData) ?
				ChartsWidget::widget([
					'type' => ChartsWidget::TYPE_DONUT,
					'containerOptions' => [
						'class' => 'col-sm-12 col-md-6 col-lg-4',
						'style' => ['height' => '50vh',],
					],
					'id' => 'chart-leads-sources-count' . $searchModel->getUniqueId(),
					'legendFormatterAsSeriesWithCount' => true,
					'showDonutTotalLabels' => true,
					'series' => $sourcesData['series'],
					'options' => [
						'labels' => $sourcesData['labels'],
						'title' => [
							'text' => Yii::t('lead', 'Sources Count'),
							'align' => 'center',
						],
						'legend' => [
							'position' => 'bottom',
							//'width' => 200,
							'height' => '55',
						],
					],
				])
				: ''
			?>


		</div>


		<div class="row">

			<?php
			//			isset($campaignsData['series']) ?
			//				ChartsWidget::widget([
			//					'type' => 'donut',
			//					'containerOptions' => [
			//						'class' => 'col-sm-12 col-md-6 col-lg-4',
			//						//		'style' => ['height' => '50vh',],
			//					],
			//					'id' => 'chart-leads-campaigns-count' . $searchModel->getUniqueId(),
			//					'legendFormatterAsSeriesWithCount' => true,
			//					'series' => $campaignsData['series'],
			//					'options' => [
			//						'labels' => $campaignsData['labels'],
			//						'title' => [
			//							'text' => Yii::t('lead', 'Campaigns Count'),
			//							'align' => 'center',
			//						],
			//						'legend' => [
			//							'position' => 'bottom',
			//							'height' => '55',
			//						],
			//						'plotOptions' => [
			//							'pie' => [
			//								'donut' => [
			//									'labels' => [
			//										'show' => true,
			//										'total' => [
			//											'show' => true,
			//											'showAlways' => true,
			//											'label' => Yii::t('common', 'Sum'),
			//										],
			//									],
			//								],
			//							],
			//						],
			//					],
			//				])
			//				: ''
			?>


			<?= isset($providersData['series']) ?
				ChartsWidget::widget([
					'type' => ChartsWidget::TYPE_DONUT,
					'containerOptions' => [
						'class' => 'col-sm-12 col-md-6 col-lg-4',//		'style' => ['height' => '50vh',],
					],
					'id' => 'chart-leads-providers-count' . $searchModel->getUniqueId(),
					'legendFormatterAsSeriesWithCount' => true,
					'showDonutTotalLabels' => true,
					'series' => $providersData['series'],
					'options' => [
						'labels' => $providersData['labels'],
						'title' => [
							'text' => Yii::t('lead', 'Provider Count'),
							'align' => 'center',
						],
						'legend' => [
							'position' => 'bottom',
							'height' => '55',
						],
					],
				])
				: ''
			?>

			<?= $searchModel->visibleHoursChart
				? $this->render('_hoursChart', [
					'model' => $searchModel,
					'chartContainerOptions' => ['class' => 'col-sm-12 col-md-8',],
				])
				: ''
			?>


			<?php

			//				ChartsWidget::widget([
			//					'type' => 'donut',
			//					'containerOptions' => [
			//						'class' => 'col-sm-12 col-md-6 col-lg-4',
			//						//		'style' => ['height' => '50vh',],
			//					],
			//					'id' => 'chart-leads-campaigns-cost' . $searchModel->getUniqueId(),
			//					'legendFormatterAsSeriesWithCount' => true,
			//					'series' => $campaignsCostData['series'],
			//					'options' => [
			//						'labels' => $campaignsCostData['labels'],
			//						'title' => [
			//							'text' => Yii::t('lead', 'Campaigns Costs'),
			//							'align' => 'center',
			//						],
			//						'legend' => [
			//							'position' => 'bottom',
			//							'height' => '55',
			//						],
			//						'plotOptions' => [
			//							'pie' => [
			//								'donut' => [
			//									'labels' => [
			//										'show' => true,
			//										'total' => [
			//											'show' => true,
			//											'showAlways' => true,
			//											'label' => Yii::t('common', 'Sum'),
			//										],
			//									],
			//								],
			//							],
			//						],
			//					],
			//				])
			?>


		</div>


		<?php if (!empty($leadsCountSeries)): ?>
			<div class="row">

				<div class="col-md-12" style="height:50vh">
					<?= ChartsWidget::widget([
						'type' => ChartsWidget::TYPE_BAR,
						'series' => array_values($leadsCountSeries),
						'height' => '100%',
						'chart' => [
							'stacked' => true,
							'zoom' => [
								'enabled' => true,
								'type' => 'x',
								'autoScaleYaxis' => true,
							],
						],
						'options' => [
							'colors' => $leadsDaysData['colors'],
							'plotOptions' => [
								'bar' => [
									'dataLabels' => [
										'total' => [
											'enabled' => true,
											'position' => 'bottom',
											'offsetX' => 6,
										],
									],
								],
							],
							'xaxis' => [
								'type' => 'datetime',
								'categories' => $leadDates,
								'labels' => ['datetimeFormatter' => ['hour' => ''],],
							],
						],
					]) ?>
				</div>
			</div>
		<?php endif; ?>


		<div class="clearfix"></div>

	</div>


