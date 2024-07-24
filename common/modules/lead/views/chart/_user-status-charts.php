<?php

use common\helpers\ArrayHelper;
use common\helpers\Html;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\LeadUser;
use common\modules\lead\models\searches\LeadChartSearch;
use common\widgets\charts\ChartsWidget;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel LeadChartSearch */
/* @var $usersNames string[] */

$usersNames = LeadChartSearch::getUsersNames(LeadUser::TYPE_OWNER);

$leadStatusColor = $searchModel->getLeadStatusColor();

$usersStatusData = $searchModel->getLeadsUserStatusData();

$userCounts = [];
$groupsOrStatuses = [];
foreach ($usersStatusData as $data) {
	$costValue = $data['costValue'];
	$costCount = $data['costCount'];
	$count = (int) $data['count'];
	$userId = $data['user_id'];
	$statusId = $data['status_id'];
	if (!isset($userCounts[$userId])) {
		$userCounts[$userId] = [
			'name' => $usersNames[$userId],
			'count' => 0,
			'data' => [],
			'totalLeadsCostValue' => 0,
			'statusCostValue' => [],
		];
	}
	$userCounts[$userId]['count'] += $count;
	$userCounts[$userId]['totalLeadsCostValue'] += $costValue;

	if (!isset($userCounts[$userId]['statusCostValue'][$statusId])) {
		$userCounts[$userId]['statusCostValue'][$statusId] = [
			'cost' => 0,
			'count' => 0,
		];
	}
	$userCounts[$userId]['statusCostValue'][$statusId]['cost'] += $costValue;
	$userCounts[$userId]['statusCostValue'][$statusId]['count'] += $costCount;

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

$costsUsersData = [];
foreach ($userCounts as $userId => $data) {
	if (!empty($data['totalLeadsCostValue'])) {
		$series = [
			'name' => $data['name'],
			'data' => [],
		];
		foreach ($data['statusCostValue'] as $statusId => $costValue) {
			$series['data'][] = [
				'x' => LeadStatus::getNames()[$statusId],
				'y' => (int) $costValue['cost'],
			];
		}
		$costsUsersData[] = $series;
	}
}

$costsUsersData = [];
foreach ($userCounts as $userId => $data) {
	if (!empty($data['totalLeadsCostValue'])) {
		foreach ($data['statusCostValue'] as $statusId => $costValue) {
			if (!isset($costsUsersData[$statusId])) {
				$costsUsersData[$statusId] = [
					'name' => LeadStatus::getNames()[$statusId],
					'data' => [],
				];
			}
			$count = $costValue['count'];
			$value = $count ? $costValue['cost'] / $count : 0;
			$costsUsersData[$statusId]['data'][] = [
				'x' => $data['name'],
				'y' => (int) $value,
			];
		}
	}
}

$groupSeries = [];

foreach ($groupsOrStatuses as $groupOrId => $group) {
	if (!isset($groupSeries[$group])) {
		$color = is_int($groupOrId) ? $leadStatusColor->getStatusColorById($groupOrId) : $leadStatusColor->getStatusColorByGroup($groupOrId);
		$groupSeries[$group] = [
			'name' => $group,
			'data' => [],
			'group' => 'status',
			'type' => ChartsWidget::TYPE_COLUMN,
			'color' => $color,
			'strokeWidth' => 0,
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

$hasCosts = !empty(array_filter($userCounts, function (array $data): bool {
	return !empty($data['totalLeadsCostValue']);
}));

if ($hasCosts) {
	$groupSeries['totalCostValue'] = [
		'name' => Yii::t('lead', 'Total Costs Value'),
		'type' => 'line',
		'data' => [],
		'color' => 'yellow',
		'strokeWidth' => 1,
	];

	$groupSeries['singleCostValue'] = [
		'name' => Yii::t('lead', 'Single Costs Value'),
		'type' => 'line',
		'data' => [],
		'color' => '#6600CC',
		'strokeWidth' => 3,
	];

	foreach ($userCounts as $data) {
		$totalCost = round($data['totalLeadsCostValue'], 2);
		$count = $data['count'];
		if ($totalCost) {
			$groupSeries['totalCostValue']['data'][] = $totalCost;
			$costCount = 0;
			foreach ($data['statusCostValue'] as $statusCosts) {
				$costCount += $statusCosts['count'];
			}
			$groupSeries['singleCostValue']['data'][] = $totalCost ? round($totalCost / $costCount, 2) : 0;
		} else {
			$groupSeries['totalCostValue']['data'][] = 0;
			$groupSeries['singleCostValue']['data'][] = 0;
		}
	}
}

unset($groupSeries['totalCostValue']);

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
	$count = $searchModel->groupedStatus === LeadChartSearch::STATUS_GROUP_DISABLE
		? count($group['data'])
		: array_sum($group['data']);
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
$yaxis = [];
if ($hasCosts) {
	$leadsSeriesNames = ArrayHelper::getColumn($groupSeries, 'name');
	$notLeadsSeriesNames = ['totalCostValue', 'singleCostValue'];
	foreach ($notLeadsSeriesNames as $notLeadsSeriesName) {
		unset($leadsSeriesNames[$notLeadsSeriesName]);
	}
	$yaxis = [

		[
			'seriesName' => array_values($leadsSeriesNames),
			'title' => [
				'text' => Yii::t('lead', 'Leads'),
			],
			'decimalsInFloat' => 0,
			'labels' => [
				'formatter' => new JsExpression('function (val) { return val.toString();}'),
			],
		],
		[
			'opposite' => true,
			'seriesName' => [
				//	Yii::t('lead', 'Total Costs Value'),
				Yii::t('lead', 'Single Costs Value'),
			],
			'title' => [
				'text' => Yii::t('lead', 'Single Costs Value'),
			],
			'decimalsInFloat' => 0,
			'labels' => [
				'formatter' => new JsExpression('function (val) { return val.toString() + " zł";}'),
			],
		],

	];

	$totalCostData = [];
	$allTotalCosts = 0;
	$usersCostData = [];
	foreach ($userCounts as $user) {
		$allTotalCosts += $user['totalLeadsCostValue'];
	}
	$i = 0;
	foreach ($userCounts as $userId => $userData) {
		$i++;
		$value = round($userData['totalLeadsCostValue']);
		if ($value) {
			$totalCostData['data'][] = $value;
			$totalCostData['labels'][] = $usersNames[$userId];
		}
	}
	$totalCostData['totalValue'] = $allTotalCosts;
}
?>

<div class="user-status-charts">


	<div class="row">

		<div class="col-sm-12 col-md-8">
			<?= !empty($group) ?
				ChartsWidget::widget([
					'id' => 'line-leads-users-count',
					'type' => ChartsWidget::TYPE_LINE,
					'series' => array_values($groupSeries),
					'height' => '520px',
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
							'width' => ArrayHelper::getColumn($groupSeries, 'strokeWidth', false),
							'curve' => 'straight',
							'curve' => 'smooth',
							//		'curve' => 'stepline',
						],
						//					'markers' => [
						//						'size' => 10,
						//						'shape' => 'rect',
						//					],
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
						'yaxis' => $yaxis,
						'tooltip' => [
							'shared' => true,
							'fixed' => [
								'enabled' => true,
								'position' => 'bottomRight',
							],
							'y' => [
								//	'formatter' => new JsExpression('function (val) { return val.toString() + "dasda";}'),
								'title' => [
									'formatter' =>
										new JsExpression('function (seriesName,x,y,z) { 
								//	console.log(x, y, z);
									return seriesName;}'),
								],
							],
						],
					],
				]) : '' ?>
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
					'type' => ChartsWidget::TYPE_DONUT,
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
							'height' => 120,//	'width' => '100',
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


		<?php endif; ?>
	</div>

	<div class="clearfix"></div>

	<div class="row">

		<div class="col-md-8">
			<?= !empty($costsUsersData)
				? ChartsWidget::widget([
					'series' => array_values($costsUsersData),
					'type' => ChartsWidget::TYPE_BAR,
					'options' => [
						'xaxis' => [
							'type' => 'category',
						],
					],
				])
				: '' ?>
		</div>
		<div class="col-md-4">

			<?= empty($totalCostData)
				?
				ChartsWidget::widget([
					'id' => 'donut-leads-users-total-cost',
					'type' => ChartsWidget::TYPE_DONUT,
					'series' => $totalCostData['data'],
					'options' => [
						'title' => [
							'text' => Yii::t('lead', 'Leads Total Cost'),
							'align' => 'center',
						],
						'labels' => array_values($totalCostData['labels']),
						'legend' => [
							'position' => 'bottom',
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
					'legendFormatterAsSeriesWithCount' => true,

				])
				: ''
			?>
		</div>
	</div>

</div>
