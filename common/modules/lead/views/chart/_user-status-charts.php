<?php

use common\helpers\ArrayHelper;
use common\helpers\Html;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\searches\LeadChartSearch;
use common\widgets\charts\ChartsWidget;
use yii\helpers\Json;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel LeadChartSearch */
/* @var $usersNames string[] */

$leadStatusColor = $searchModel->getLeadStatusColor();

$usersStatusData = $searchModel->getLeadsUserStatusData();
$userCounts = [];
$groupsOrStatuses = [];
foreach ($usersStatusData as $data) {
	$count = $data['count'];
	$userId = $data['user_id'];
	$statusId = $data['status_id'];
	if (!isset($userCounts[$userId])) {
		$userCounts[$userId] = [
			'name' => $usersNames[$userId],
			'count' => 0,
			'data' => [],
		];
	}
	$userCounts[$userId]['count'] += $count;
	if ($searchModel->groupedStatus === LeadChartSearch::STATUS_GROUP_DISABLE) {
		$groupsOrStatuses[$statusId] = LeadStatus::getNames()[$statusId];
		$userCounts[$userId][$statusId] = $count;
	} else {
		$group = LeadStatus::getModels()[$statusId]->chart_group;
		if ($group) {
			if (!isset($userCounts[$userId]['data'][$group])) {
				$userCounts[$userId]['data'][$group] = 0;
			}
			$userCounts[$userId]['data'][$group] += $count;
			$groupsOrStatuses[$group] = $group;
		}
	}
}

uasort($userCounts, function ($a, $b) {
	return $b['count'] <=> $a['count'];
});

$groupSeries = [];

foreach ($groupsOrStatuses as $groupOrId => $group) {
	if (!isset($groupSeries[$group])) {
		$color = is_int($groupOrId) ? $leadStatusColor->getStatusColorById($groupOrId) : $leadStatusColor->getStatusColorByGroup($groupOrId);
		$groupSeries[$group] = [
			'name' => $group,
			'data' => [],
			'group' => 'status',
			'type' => 'column',
			'color' => $color,
		];
	}
}

$totalSeries = [
	'name' => Yii::t('lead', 'Leads'),
	'data' => [],
	'group' => 'Total',
	'type' => ChartsWidget::TYPE_COLUMN,
	'color' => '#aeaeae',
];
foreach ($userCounts as $userId => $data) {
	$totalSeries['data'][] = $data['count'];
	foreach ($groupsOrStatuses as $groupId) {
		if (!isset($data['data'][$groupId])) {
			$userCounts[$userId]['data'][$groupId] = 0;
		}
	}
}
foreach ($userCounts as $data) {
	foreach ($data['data'] as $groupId => $count) {
		$groupSeries[$groupId]['data'][] = $count;
	}
}

$groupSeries[$totalSeries['name']] = $totalSeries;
$statusUsersNames = array_values(ArrayHelper::getColumn($userCounts, 'name'));

$jsonGroupSeries = Json::encode($groupSeries);
$js = <<<JS
	var lastGroup = '';
	function changeGroupStatus(groupId, btn){
		document.querySelectorAll('#nav-status-groups .btn').forEach(function(element){
			element.classList.remove('active');
		});
		btn.classList.add('active');
		const series = $jsonGroupSeries;
		if(groupId !== lastGroup){
			const data = series[groupId].data;
			if(data){
				lastGroup = groupId;
				ApexCharts.exec('donut-leads-users-count', 'updateSeries', data, true);
				ApexCharts.exec('donut-leads-users-count', 'updateOptions', {title:{
					text:groupId
				}}, true);
			}
		}
	}
JS;

$this->registerJs($js, View::POS_HEAD);

foreach ($groupSeries as $group) {
	$buttons[] = [
		'label' => $group['name'] . ' - ' . array_sum($group['data']),
		'linkOptions' => [
			'class' => 'btn btn-xs',
			'style' => [
				'background-color' => $group['color'],
				'color' => 'white',
			],
			'onclick' => 'changeGroupStatus("' . $group['name'] . '", this);',
		],
	];
}

?>

<div class="user-status-charts">


	<div class="row">

		<div class="col-sm-12 col-md-8">
			<?= ChartsWidget::widget([
				'id' => 'line-leads-users-count',
				'type' => ChartsWidget::TYPE_LINE,
				'series' => array_values($groupSeries),
				//	'height' => '600',
				'chart' => [
					'stacked' => true,
					'id' => 'line-leads-users-count',
					'group' => 'users',
					'zoom' => [
						'enabled' => true,
						'type' => 'x',
					],
				],
				'options' => [
					'stroke' => [
						'show' => false,
						'width' => 0,
						//	'curve' => 'smooth',
					],
					'markers' => [
						'size' => 10,
						'shape' => 'rect',
					],
					//					'dataLabels' => [
					//						'enabled' => false,
					//					],
					'plotOptions' => [
						'bar' => [
							'horizontal' => false,
						],
					],
					'xaxis' => [
						'categories' => $statusUsersNames,
						'labels' => [
							'rotate' => -45,
						],
					],
					'yaxis' => [
						//	'max' => 200,
						//	'logarithmic' => true,
					],
					'tooltip' => [
						'shared' => true,
						'fixed' => [
							'enabled' => true,
							'position' => 'bottomRight',
						],
					],
				],
			]) ?>
		</div>

		<?php if (!empty($totalSeries)): ?>

			<div class="col-sm-12 col-md-4">

				<p>
					<?php
					foreach ($buttons as $button) {
						echo Html::button($button['label'], $button['linkOptions']) . ' ';
					}
					?>
				</p>


				<?= ChartsWidget::widget([
					'id' => 'donut-leads-users-count',
					'type' => ChartsWidget::TYPE_PIE,
					'legendFormatterAsSeriesWithCount' => true,
					'series' => $totalSeries['data'],
					'chart' => [
						'id' => 'donut-leads-users-count',
						'group' => 'users',
					],
					'options' => [
						'labels' => $statusUsersNames,
						'title' => [
							'text' => Yii::t('lead', 'Leads Users Count'),
							'align' => 'center',
						],
						'legend' => [
							'show' => false,
							//	'floating' => true,
							'position' => 'bottom',
							'height' => 120,
							//	'width' => '100',
						],

						//						'plotOptions' => [
						//							'pie' => [
						//								//	'customScale' => 2,
						//								'donut' => [
						//									//	'size' => '80%',
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
					],
				])
				?>
			</div>


		<?php endif; ?>
	</div>

	<div class="clearfix"></div>


</div>
