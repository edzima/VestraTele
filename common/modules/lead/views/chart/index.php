<?php

use common\helpers\ArrayHelper;
use common\models\user\User;
use common\modules\lead\chart\LeadStatusColor;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\LeadType;
use common\modules\lead\models\searches\LeadChartSearch;
use common\widgets\charts\ChartsWidget;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $searchModel LeadChartSearch */

$this->title = Yii::t('lead', 'Charts');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = $this->title;

$leadStatusColor = new LeadStatusColor();

$leadsDaysData = $searchModel->getLeadsByDays();

//echo '<pre>' . \yii\helpers\VarDumper::dumpAsString($leadsDaysData) . '</pre>';
$testData = [];
foreach ($leadsDaysData as $leadDaysData) {
	$day = $leadDaysData['date'];
	if (!isset($testData[$day])) {
		$testData[$day] = [
			'count' => 0,
		];
	}
	$testData[$day]['data'][$leadDaysData['status_id']] = $leadDaysData['count'];
	$testData[$day]['count'] += $leadDaysData['count'];
}

$leadsCountSeries = [];
$leadDates = array_values(array_unique(ArrayHelper::getColumn($leadsDaysData, 'date')));
$hasGroup = false;
foreach ($leadsDaysData as $data) {
	$statusId = $data['status_id'];
	if (!isset($leadsCountSeries[$statusId])) {
		$statusModel = LeadStatus::getModels()[$statusId];
		$leadsCountSeries[$statusId] = [
			'name' => $statusModel->name,
			'data' => [],
		];
		if ($statusModel->chart_group) {
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
		$leadsCountSeries[$index]['group'] = Yii::t('chart', 'Other');
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
foreach ($statusesCount as $status_id => $count) {
	$status = LeadStatus::getModels()[$status_id];
	$statusData['series'][] = $count;
	$statusData['labels'][] = $status->name;
	$statusData['colors'][] = $leadStatusColor->getStatusColor($status);
}

$statusGroupData = [];
$withoutGroup = false;
foreach ($statusesCount as $status_id => $count) {
	$status = LeadStatus::getModels()[$status_id];
	if (!$withoutGroup) {
		if (empty($status->chart_group)) {
			continue;
		}
	}
	$group = $status->chart_group ? $status->chart_group : Yii::t('chart', 'Without group');
	if (!isset($statusGroupData['series'][$group])) {
		$statusGroupData['series'][$group] = 0;
	}

	$statusGroupData['series'][$group] += $count;
	$statusGroupData['labels'][$group] = $group;
	$statusGroupData['colors'][$group] = $leadStatusColor->getStatusColor($status);
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

$usersData = $searchModel->getLeadsUsersCount();
$usersNames = User::getSelectList(array_keys($usersData), false);
foreach ($usersData as $userId => $count) {
	$name = empty($userId) ? Yii::t('lead', 'Without User') : $usersNames[$userId];
	$usersData['series'][] = $count;
	$usersData['labels'][] = $name;
}

?>
<div class="lead-chart-index">
	<?= $this->render('_search', [
		'model' => $searchModel,
		'sourcesNames' => $sourcesData['names'] ?? [],
		'usersNames' => $usersNames,
	]) ?>
	<div class="lead-charts">
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
							],
						],
					]) ?>
				</div>
			</div>
		<?php endif; ?>
		<?php

		//	echo '<pre>' . \yii\helpers\VarDumper::dumpAsString($leadsCountSeries) . '</pre>';

		?>


		<div class="row">
			<?= !empty($statusData) ?
				ChartsWidget::widget([
					'type' => 'donut',
					'containerOptions' => [
						'class' => 'col-sm-12 col-md-4',
						'style' => [
							'height' => '50vh',
						],
					],
					'id' => 'chart-leads-statuses-count' . $searchModel->getUniqueId(),
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

			<?= !empty($typesData) ?
				ChartsWidget::widget([
					'type' => ChartsWidget::TYPE_DONUT,
					'containerOptions' => [
						'class' => 'col-sm-12 col-md-4',
						'style' => [
							'height' => '50vh',
						],
					],
					'id' => 'chart-leads-types-count' . $searchModel->getUniqueId(),
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


			<?= !empty($sourcesData) ?
				ChartsWidget::widget([
					'type' => ChartsWidget::TYPE_DONUT,
					'containerOptions' => [
						'class' => 'col-sm-12 col-md-4',
						'style' => [
							'height' => '50vh',
						],
					],
					'id' => 'chart-leads-sources-count' . $searchModel->getUniqueId(),
					'series' => $sourcesData['series'],
					'options' => [
						'labels' => $sourcesData['labels'],
						'title' => [
							'text' => Yii::t('lead', 'Sources Count'),
							'align' => 'center',
						],
						'legend' => [
							'position' => 'bottom',
							//							'width' => 200,
							'height' => '55',
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

		<div class="row">
			<?= !empty($statusGroupData) ?
				ChartsWidget::widget([
					'type' => ChartsWidget::TYPE_DONUT,
					'containerOptions' => [
						'class' => 'col-sm-12 col-md-4',
						'style' => [
							'height' => '50vh',
						],
					],
					'id' => 'chart-leads-statuses-group-count' . $searchModel->getUniqueId(),
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

		<?php if (!empty($usersData)): ?>
			<div class="row">
				<div class="col-sm-12">
					<?= ChartsWidget::widget([
						'type' => ChartsWidget::TYPE_BAR,
						'id' => 'chart-leads-bar-users-count' . $searchModel->getUniqueId(),
						'height' => '340',
						'series' => [
							[
								'name' => Yii::t('lead', 'Users'),
								'data' => $usersData['series'],
							],
						],
						'chart' => [
							'stacked' => true,
							'zoom' => [
								'enabled' => true,
								'type' => 'x',
								'autoScaleYaxis' => true,
							],
						],
						'options' => [
							'labels' => $usersData['labels'],
							'title' => [
								'text' => Yii::t('lead', 'Leads Users Count'),
								'align' => 'center',
							],
							'legend' => [
								//	'position' => 'bottom',
								'width' => 200,
								'formatter' => new JsExpression('function(seriesName, opts){
							return [seriesName, " - ", opts.w.globals.series[opts.seriesIndex]];
							}'),
							],
						],
					])
					?>
				</div>
			</div>
			<div class="clearfix"></div>

		<?php endif; ?>
		<?php if (!empty($usersData)): ?>
			<div class="row">
				<?= ChartsWidget::widget([
					'type' => ChartsWidget::TYPE_DONUT,
					'containerOptions' => [
						'class' => 'col-sm-12 col-md-4',
						'style' => [
							'height' => '50vh',
						],
					],
					'id' => 'chart-leads-users-count' . $searchModel->getUniqueId(),
					'series' => $usersData['series'],
					'options' => [
						'labels' => $usersData['labels'],
						'title' => [
							'text' => Yii::t('lead', 'Leads Users Count'),
							'align' => 'center',
						],
						'legend' => [
							'position' => 'bottom',
							'height' => 100,
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
				?>
			</div>

			<div class="clearfix"></div>

		<?php endif; ?>

	</div>


