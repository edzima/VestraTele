<?php

use common\helpers\ArrayHelper;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\LeadType;
use common\modules\lead\models\LeadUser;
use common\modules\lead\models\searches\LeadChartSearch;
use common\widgets\charts\ChartsWidget;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $searchModel LeadChartSearch */

$this->title = Yii::t('lead', 'Charts');
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

$typesData = $searchModel->getLeadTypesCount();
foreach ($typesData as $typeId => $count) {
	$type = LeadType::getModels()[$typeId];
	$typesData['series'][] = $count;
	$typesData['labels'][] = $type->name;
}

$sourcesData = $searchModel->getLeadSourcesCount();
foreach ($sourcesData as $id => $count) {
	$source = LeadSource::getModels()[$id];
	$sourcesData['series'][] = $count;
	$sourcesData['labels'][] = $source->name;
	$sourcesData['names'][$id] = $source->name;
}

$usersNames = $searchModel::getUsersNames(LeadUser::TYPE_OWNER);

?>
<div class="lead-chart-index">
	<?= $this->render('_search', [
		'model' => $searchModel,
		'sourcesNames' => $sourcesData['names'] ?? [],
		'usersNames' => $usersNames,
	]) ?>
	<div class="lead-charts">

		<?= $this->render('_user-status-charts', [
			'searchModel' => $searchModel,
			'usersNames' => $usersNames,
		]) ?>

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

			<?= !empty($statusGroupData) && $searchModel->groupedStatusChartType === ChartsWidget::TYPE_DONUT ?
				ChartsWidget::widget([
					'type' => ChartsWidget::TYPE_DONUT,
					'containerOptions' => [
						'class' => 'col-sm-12 col-md-6 col-lg-4 status-charts',
						'style' => ['height' => '50vh',],
					],
					'id' => 'chart-leads-statuses-group-count' . $searchModel->getUniqueId(),
					'legendFormatterAsSeriesWithCount' => true,
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
			<?= !empty($statusData) ?
				ChartsWidget::widget([
					'type' => 'donut',
					'containerOptions' => [
						'class' => 'col-sm-12 col-md-6 col-lg-4 status-charts',
						'style' => ['height' => '50vh',],
					],
					'id' => 'chart-leads-statuses-count' . $searchModel->getUniqueId(),
					'legendFormatterAsSeriesWithCount' => true,
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


			<?= !empty($typesData) ?
				ChartsWidget::widget([
					'id' => 'chart-leads-types-count' . $searchModel->getUniqueId(),
					'type' => ChartsWidget::TYPE_DONUT,
					'containerOptions' => [
						'class' => 'col-sm-12 col-md-6 col-lg-4',
						'style' => ['height' => '50vh',],
					],
					'legendFormatterAsSeriesWithCount' => true,
					'series' => $typesData['series'],
					'options' => [
						'labels' => $typesData['labels'],
						'title' => [
							'text' => Yii::t('lead', 'Types Count'),
							'align' => 'center',
						],
						'legend' => [
							'position' => 'bottom',
							//'width' => 200,
							'height' => '55',
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

			<?= !empty($sourcesData) ?
				ChartsWidget::widget([
					'type' => ChartsWidget::TYPE_DONUT,
					'containerOptions' => [
						'class' => 'col-sm-12 col-md-6 col-lg-4',
						'style' => ['height' => '50vh',],
					],
					'id' => 'chart-leads-sources-count' . $searchModel->getUniqueId(),
					'legendFormatterAsSeriesWithCount' => true,
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


