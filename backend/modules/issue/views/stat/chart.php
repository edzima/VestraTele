<?php

use backend\modules\issue\models\IssueStats;
use onmotion\apexcharts\ApexchartsWidget;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model IssueStats */

$this->title = Yii::t('backend', 'Stats - Chart');

$this->params['issueParentTypeNav'] = [
	'route' => ['/issue/stat/chart'],
];

$yearsModel = $model->getYearsModels(SORT_DESC);

$series = [];
$data = [];
$dataArchives = [];
$dataWithPays = [];
$categories = [];
$today = new DateTime();
foreach ($yearsModel as $yearModel) {
//	$data = [];
	foreach ($yearModel->getMonthModels(SORT_DESC) as $monthModel) {
		$date = new DateTime($monthModel->getLastDayOfMonthDate());
		if ($date > $today) {
			$date = $today;
		}
		$date = date("{$monthModel->year}-{$monthModel->month}-16");
		$data[] = [
			'x' => $date,
			'y' => $monthModel->getCountForMonth(),
		];
//		$dataArchives[] = [
//			'x' => $date,
//			'y' => $monthModel->getArchivesCountForMonth(),
//		];
		$dataWithPays[] = [
			'x' => $date,
			'y' => $monthModel->getWithPaysCountForMonth(),
		];
		//$categories[] = Yii::$app->formatter->asDate($monthModel->getFirstDayOfMonthDate(), 'LLLL Y');
	}
//	$series[] =[
//		'name' => 	$yearModel->year,
//		'data' => $data
//	];
}
$series[] = [
	'name' => 'Sprawy',
	'data' => $data,
	'type' => 'column',
];
//$series[] = [
//	'name' => 'Archiwum',
//	'data' => $dataArchives,
//];
$series[] = [
	'name' => 'Rozliczenia',
	'data' => $dataWithPays,
	'type' => 'line',
];

echo ApexchartsWidget::widget([
	'type' => 'line',
	//'type' => 'rangeBar',
	'height' => '400',
	'series' => $series,
	'chartOptions' => [
		'stacked' => true,
		'defaultLocale' => 'pl',
		'locales' => ['pl'],
		'dataLabels' => [
			'formatter' => new JsExpression('function (val, { seriesIndex, dataPointIndex, w }) {
				return val;
				if(dataPointIndex > 0){
					let previousValue = w.config.series[seriesIndex].data[dataPointIndex-1].y;
					console.log(previousValue);
					let diff = val-previousValue;
					if(diff ===0){
						return "";
					}
					if(diff>1){
						return "+ "+diff;
					}
					return diff;
				}
				
		        return val;
               
            }'),
		],
		'xaxis' => [
			//	'type' => 'category',
			'type' => 'datetime',
			//			'labels' => [
			//				'format' => 'y/MMM',
			//			],
			//	'categories' => $categories,
		],
		//		'plotOptions' => [
		////			'bar' => [
		////			//	'horizontal' => true,
		////			],
		//		],

	],
]);
$yearsModel = $model->getYearsModels();
?>

<div class="issue-stats-index">

	<h2>Zarejestrowane Sprawy</h2>

	<?php foreach ($yearsModel as $year => $yearModel): ?>
		<div class="row">
			<div class="col-md-2">
				<div class="info-box bg-blue">
					<span class="info-box-icon"><i class="fa fa-suitcase"></i></span>
					<div class="info-box-content">
						<span class="info-box-text"><?= $year ?></span>
						<span class="info-box-number"><?= $yearModel->getCountForYear() ?></span>
						<?php if ($yearModel->getAllCount() !== $yearModel->getCountForYear()): ?>
							<div class="progress">
								<div class="progress-bar" style="width: <?= $yearModel->getCountForYear() / $yearModel->getAllCount() * 100 ?>%"></div>
							</div>
							<span class="progress-description">
	                    <?= $yearModel->getCountForYear() . '/' . $yearModel->getAllCount() ?></span>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<div class="issue-create-count-grid col-md-10">

				<?php
				$monthsModels = $yearModel->getMonthModels();
				foreach ($monthsModels as $month => $monthModel) {
					if ($month > 1) {
						$comparativeModel = $monthsModels[$month - 1] ?? null;
					} else {
						$previousYear = $yearsModel[$year - 1] ?? null;
						if ($previousYear) {
							$comparativeModel = $previousYear->getMonthModels()[12] ?? null;
						}
					}
					echo $this->render('_month', [
						'model' => $monthModel,
						'comparativeModel' => $comparativeModel,
					]);
				}
				?>

			</div>
		</div>
		<br><br>
	<?php endforeach; ?>


</div>


