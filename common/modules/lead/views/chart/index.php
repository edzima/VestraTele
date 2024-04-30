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

$usersData = $searchModel->getLeadsUsersCount();
$users = User::getSelectList(array_keys($usersData));
foreach ($usersData as $userId => $count) {
	$name = empty($userId) ? Yii::t('lead', 'Without User') : $users[$userId];
	$usersData['series'][] = $count;
	$usersData['labels'][] = $name;
}

$statusData = $searchModel->getLeadStatusesCount();
$leadStatusColor = new LeadStatusColor();

foreach ($statusData as $status_id => $count) {
	$status = LeadStatus::getModels()[$status_id];
	$statusData['series'][] = $count;
	$statusData['labels'][] = $status->name;
	$statusData['colors'][] = $leadStatusColor->getStatusColor($status);
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
}
$leadsDaysData = $searchModel->getLeadsByDays();
$leadsCountSeries = [];
$leadDates = array_values(array_unique(ArrayHelper::getColumn($leadsDaysData, 'date')));
foreach ($leadsDaysData as $data) {
	$statusId = $data['status_id'];
	if (!isset($leadsCountSeries[$statusId])) {
		$statusModel = LeadStatus::getModels()[$statusId];
		$leadsCountSeries[$statusId] = [
			'name' => $statusModel->name,
			'data' => [],
		];
		//@todo add chart_group column to LeadStatus
		if ($statusModel->name === 'Umowa') {
			$leadsCountSeries[$statusId]['group'] = 'Umowa';
		} else {
			$leadsCountSeries[$statusId]['group'] = 'Reszta';
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
	$data = $data['data'];
	$dataWithoutStatues = [];
	foreach ($leadDates as $date) {
		$dataWithoutStatues[] = isset($data[$date]) ? $data[$date] : 0;
	}

	$leadsCountSeries[$index]['data'] = $dataWithoutStatues;
}

//foreach ($leadsCountSeries as $index => $data) {
//	while (count($leadsCountSeries[$index]['data']) <= 10) {
//	//	$leadsCountSeries[$index]['data'][] = 0;
//	}
//}

?>
<div class="lead-chart-index">
	<?= $this->render('_search', [
		'model' => $searchModel,
	]) ?>

	<div class="lead-charts">
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

		<?php

		//	echo '<pre>' . \yii\helpers\VarDumper::dumpAsString($leadsCountSeries) . '</pre>';

		?>

		<div class="row">
			<div class="col-sm-12 col-md-4" style="height:50vh">
				<?= !empty($statusData) ?
					ChartsWidget::widget([
						'type' => 'donut',
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
								//		'position' => 'bottom',
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

			<div class="col-sm-12 col-md-4" style="height:50vh">
				<?= !empty($typesData) ?
					ChartsWidget::widget([
						'type' => ChartsWidget::TYPE_DONUT,
						'id' => 'chart-leads-types-count' . $searchModel->getUniqueId(),
						'series' => $typesData['series'],
						'options' => [
							'labels' => $typesData['labels'],
							'title' => [
								'text' => Yii::t('lead', 'Types Count'),
								'align' => 'center',
							],
							'legend' => [
								//		'position' => 'bottom',
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

			<div class="col-sm-12 col-md-4" style="height:50vh">
				<?= !empty($sourcesData) ?
					ChartsWidget::widget([
						'type' => 'donut',
						'id' => 'chart-leads-sources-count' . $searchModel->getUniqueId(),
						'series' => $sourcesData['series'],
						'options' => [
							'labels' => $sourcesData['labels'],
							'title' => [
								'text' => Yii::t('lead', 'Sources Count'),
								'align' => 'center',
							],
							'legend' => [
								//		'position' => 'bottom',
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
		</div>

		<?php if (!empty($usersData)): ?>
		<div class="row">
			<div class="col-sm-12"
			">
			<?= ChartsWidget::widget([
				'type' => ChartsWidget::TYPE_BAR,
				'id' => 'chart-leads-bar-users-count' . $searchModel->getUniqueId(),
				'height' => '300',
				'series' => [
					[
						'name' => Yii::t('lead', 'Users'),
						'data' => $usersData['series'],
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
			<div class="col-sm-12 col-md-6" style="height:30vh">
				<?= ChartsWidget::widget([
					'type' => ChartsWidget::TYPE_DONUT,
					'id' => 'chart-leads-users-count' . $searchModel->getUniqueId(),
					'series' => $usersData['series'],
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
		</div>

		<div class="clearfix"></div>

	<?php endif; ?>

</div>


