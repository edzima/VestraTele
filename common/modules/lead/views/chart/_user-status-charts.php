<?php

use common\helpers\ArrayHelper;
use common\modules\lead\models\LeadStatus;
use common\modules\lead\models\LeadUser;
use common\modules\lead\models\searches\LeadChartSearch;
use common\widgets\charts\ChartsWidget;
use yii\bootstrap\Nav;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel LeadChartSearch */
/* @var $usersNames string[] */

$usersNames = LeadChartSearch::getUsersNames(LeadUser::TYPE_OWNER);
foreach ($usersNames as &$name) {
	$names = explode(' ', $name);
	$shortName = $names[0];
	if (isset($names[1])) {
		$shortName .= ' ' . substr($names[1], 0, 1) . '.';
	}
	$name = $shortName;
}

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
		$userCounts[$userId]['data'][$statusId] = $count;
		//$userCounts[$userId][$statusId] = $count;
	} else {
		$group = LeadStatus::getModels()[$statusId]->chart_group;
		if ($group) {
			$groupsOrStatuses[$group] = $group;
			if (!isset($userCounts[$userId]['data'][$group])) {
				$userCounts[$userId]['data'][$group] = 0;
			}
			$userCounts[$userId]['data'][$group] += $count;
		}
	}
}

uasort($userCounts, function ($a, $b) {
	return $b['count'] <=> $a['count'];
});

$costsUsersData = [];
$costsUsers = [];

foreach ($userCounts as $userId => $data) {
	if (!empty($data['totalLeadsCostValue'])) {
		foreach ($data['statusCostValue'] as $statusId => $costValue) {
			if (!isset($costsUsersData[$statusId])) {
				$costsUsersData[$statusId] = [
					'name' => LeadStatus::getNames()[$statusId],
					'data' => [],
					'color' => $searchModel->getLeadStatusColor()->getStatusColorById($statusId),
					'type' => ChartsWidget::TYPE_COLUMN,
					'strokeWidth' => 0,
				];
			}
			$count = $costValue['count'];
			if ($count) {
				$value = $costValue['cost'] / $count;
				$name = $data['name'];
				$costsUsersData[$statusId]['costValue'][$userId] = $costValue;
				$costsUsersData[$statusId]['data'][] = [
					'x' => $name,
					'y' => (int) $value,
					'user_id' => $userId,
				];
				$costsUsers[$userId] = $name;
			}
		}
	}
}

$groupSeries = [];
foreach ($groupsOrStatuses as $groupOrId => $group) {
	if (!isset($groupSeries[$groupOrId])) {
		$color = is_int($groupOrId) ? $leadStatusColor->getStatusColorById($groupOrId) : $leadStatusColor->getStatusColorByGroup($groupOrId);
		$groupSeries[$groupOrId] = [
			'name' => $group,
			'data' => [],
			'group' => 'status',
			'type' => ChartsWidget::TYPE_COLUMN,
			'color' => $color,
			'strokeWidth' => 0,
			'yAxis' => [
				'seriesName' => Yii::t('lead', 'Leads'),
				'title' => [
					'text' => Yii::t('lead', 'Leads'),
				],
				'decimalsInFloat' => 0,
				'labels' => [
					'formatter' => new JsExpression('function (val) { return val.toString();}'),
				],
			],
		];
	}
}

$totalSeries = [
	'name' => Yii::t('lead', 'Leads'),
	'data' => [],
	'group' => 'Total',
	'type' => ChartsWidget::TYPE_COLUMN,
	'color' => '#aeaeae',
	'strokeWidth' => 0,
	'yAxis' => [
		'seriesName' => Yii::t('lead', 'Leads'),
		'title' => [
			'text' => Yii::t('lead', 'Leads'),
		],
		'decimalsInFloat' => 0,
		'labels' => [
			'formatter' => new JsExpression('function (val) { return val.toString();}'),
		],
	],
];
foreach ($userCounts as $userId => $data) {
	$totalSeries['data'][] = $data['count'];
	foreach ($groupsOrStatuses as $groupId => $name) {
		if (!isset($data['data'][$groupId])) {
			$userCounts[$userId]['data'][$groupId] = null;
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
		'withoutCount' => true,
		'currencyFormatter' => true,
		'countAsAvg' => true,
		'yAxis' => [
			'opposite' => true,
			'seriesName' => [
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

	foreach ($userCounts as $data) {
		$totalCost = $data['totalLeadsCostValue'];
		$count = $data['count'];
		if ($totalCost) {
			$groupSeries['totalCostValue']['data'][] = $totalCost;
			$costCount = 0;
			foreach ($data['statusCostValue'] as $statusCosts) {
				$costCount += $statusCosts['count'];
			}
			$groupSeries['singleCostValue']['data'][] = $totalCost ? round($totalCost / $costCount) : 0;
		} else {
			$groupSeries['totalCostValue']['data'][] = 0;
			$groupSeries['singleCostValue']['data'][] = 0;
		}
	}

	$costsUsersData = array_filter($costsUsersData, function (array $data) {
		return !empty($data['data']);
	});
	foreach ($costsUsersData as $index => $data) {
		$x = ArrayHelper::getColumn($data['data'], 'x', false);

		// users without costs in series.
		$diff = array_diff($costsUsers, $x);
		foreach ($diff as $name) {
			$costsUsersData[$index]['data'][] = [
				'x' => $name,
				'y' => 0,
			];
		}
	}

	$costUsersNames = [];

	$firstCost = reset($costsUsersData);
	$newCostsAvg = [];
	foreach ($costsUsersData as $data) {
		foreach ($data['costValue'] as $userID => $costData) {
			if (!isset($newCostsAvg[$userID])) {
				$newCostsAvg[$userID] = [
					'count' => 0,
					'cost' => 0,
					'x' => $usersNames[$userID],
				];
			}
			if (isset($costData['count'])) {
				$newCostsAvg[$userID]['count'] += $costData['count'];
				$newCostsAvg[$userID]['cost'] += $costData['cost'];
			}
		}
	}

	foreach ($newCostsAvg as $userId => $costData) {
		$avg = $costData['cost'] / $costData['count'];
		$newCostsAvg[$userId]['avg'] = $avg;
	}

	usort($newCostsAvg, function (array $a, array $b) {
		return $a['avg'] <=> $b['avg'];
	});

	$newCostAvgMap = ArrayHelper::map($newCostsAvg, 'x', 'avg');
	$costAvgData = [];
	foreach ($newCostAvgMap as $x => $y) {
		$costAvgData[] = [
			'x' => $x,
			'y' => $y,
		];
	}

//	maybe must be te same order.
	foreach ($costsUsersData as $index => &$data) {
		uasort($data['data'], function ($a, $b) use ($newCostAvgMap) {
			return $newCostAvgMap[$a['x']] <=> $newCostAvgMap[$b['x']];
		});
		$costsUsersData[$index]['data'] = array_values($data['data']);
	}

	$costsUsersData['avg'] = [
		'name' => Yii::t('lead', 'Single Costs Value'),
		'data' => $costAvgData,
		'type' => 'line',
		'strokeWidth' => 3,
		'color' => '#6600CC',
	];
}
unset($groupSeries['totalCostValue']);

if (!empty($totalSeries['data'])) {
	$groupSeries[$totalSeries['name']] = $totalSeries;
}

$statusUsersNames = array_values(ArrayHelper::getColumn($userCounts, 'name'));

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
		],
		[
			'opposite' => true,
			'seriesName' => Yii::t('lead', 'Single Costs Value'),
			'title' => [
				'text' => Yii::t('lead', 'Single Costs Value'),
			],
			'decimalsInFloat' => 0,
		],

	];

	$totalCostData = [];
	foreach ($userCounts as $userId => $userData) {
		$value = round($userData['totalLeadsCostValue']);
		if ($value) {
			$totalCostData['data'][$userId] = $value;
		}
	}
	uasort($totalCostData['data'], function ($a, $b) {
		return $b <=> $a;
	});
	$totalCostData['labels'] = [];
	foreach ($totalCostData['data'] as $userId => $value) {
		$totalCostData['labels'][] = $usersNames[$userId];
	}
}

//pie can't have null values
$pieGroupSeries = $groupSeries;
foreach ($pieGroupSeries as $index => $serie) {
	foreach ($serie['data'] as $dataIndex => $value) {
		if (empty($value)) {
			$pieGroupSeries[$index]['data'][$dataIndex] = 0;
		}
	}
}
$jsonGroupSeries = Json::encode($pieGroupSeries);
$currencyFormatter = (string) ChartsWidget::currencyFormatterExpression();
$js = <<<JS
	var lastGroup = '';
	function changeGroupStatus(groupId, btn){
		document.querySelectorAll('#nav-status-groups .btn').forEach(function(element){
			element.classList.remove('active');
		});
		btn.classList.add('active');
		const series = $jsonGroupSeries;
		const currencyFormatter = $currencyFormatter;
		
		if(groupId !== lastGroup){
			var data = null;
			var serie = null;
			for (const [key, value] of Object.entries(series)) {
				if(value.name === groupId){
					data = value.data;
					serie = value;
					break;
				}
			}
			if(data){
				ApexCharts.exec('donut-leads-users-count', 'updateSeries', data, true);
				// if(serie.currencyFormatter){
				// 	ApexCharts.exec('donut-leads-users-count', 'updateOptions', {
				// 		dataLabels:{
				// 			formatter: currencyFormatter,
				// 		}
				// 	}, true);
				// }
				// else{
				// 	ApexCharts.exec('donut-leads-users-count', 'updateOptions', {
				// 		dataLabels:{
				// 			formatter: function(val){return val.toFixed(1) + '%'}
				// 		}
				// 	}, true);
				// }
				
		
				ApexCharts.exec('donut-leads-users-count', 'updateOptions', {title:{
					text:groupId
				}}, true);
			}
		}
	}
JS;

$this->registerJs($js, View::POS_HEAD);
$groupButtons = [];
foreach ($groupSeries as $group) {
	$name = $group['name'];

	if (!isset($group['withoutCount'])) {
		$count = $searchModel->groupedStatus === LeadChartSearch::STATUS_GROUP_DISABLE
			? count($group['data'])
			: array_sum($group['data']);
		if (isset($group['countAsAvg'])) {
			$count = array_sum($group['data']) / count($group['data']);
		}

		$count = round($count);
		if (isset($group['currencyFormatter'])) {
			$count = Yii::$app->formatter->asCurrency($count);
		}
		$label = $name . ' - ' . $count;
	} else {
		$label = $name;
	}
	$groupButtons[] = [
		'label' => $label,
		'linkOptions' => [
			'class' => 'btn btn-sm text-uppercase',
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


	<p>
		<?= Nav::widget([
			'items' => $groupButtons,
			'options' => ['class' => 'nav-pills'],
			'id' => 'nav-status-groups',
		]) ?>

	</p>

	<div class="row">


		<?php if (!empty($totalSeries)): ?>

			<div class="col-sm-12 col-md-4">

				<?= !empty($totalSeries['data'])
					? ChartsWidget::widget([
						'id' => 'donut-leads-users-count',
						'type' => ChartsWidget::TYPE_DONUT,
						'legendFormatterAsSeriesWithCount' => true,
						'showDonutTotalLabels' => true,
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
						],
					])
					: ''
				?>
			</div>


		<?php endif; ?>
		<div class="col-sm-12 col-md-8">
			<?= !empty($groupSeries) ?
				ChartsWidget::widget([
					'id' => 'line-leads-users-count',
					'type' => ChartsWidget::TYPE_AREA,
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
							'hideEmptySeries' => true,
							'fixed' => [
								'enabled' => true,
								'position' => 'bottomRight',
							],
						],
					],
				]) : '' ?>
		</div>

	</div>

	<div class="clearfix"></div>

	<div class="row">

		<div class="col-md-8">

			<?= !empty($costsUsersData)
				? ChartsWidget::widget([
					'series' => array_values($costsUsersData),
					'type' => ChartsWidget::TYPE_LINE,

					'height' => '420px',
					'options' => [
						'title' => [
							'text' => Yii::t('lead', 'Leads Users Costs'),
							'align' => 'center',
						],
						'stroke' => [
							'width' => ArrayHelper::getColumn($costsUsersData, 'strokeWidth', false),
							'curve' => 'smooth',
						],
						'xaxis' => [
							'type' => 'category',
							'labels' => [
								'rotate' => -45,
							],
						],
						'yaxis' => [
							'showForNullSeries' => false,
							'decimalsInFloat' => 0,
							'labels' => [
								'formatter' => ChartsWidget::currencyFormatterExpression(),
							],
						],
						'tooltip' => [
							'hideEmptySeries' => true,
						],
					],
				])
				: '' ?>


		</div>
		<div class="col-md-4">

			<?= !empty($totalCostData)
				? ChartsWidget::widget([
					'id' => 'donut-leads-users-total-cost',
					'type' => ChartsWidget::TYPE_DONUT,
					'series' => array_values($totalCostData['data']),
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
					],
					'legendFormatterAsSeriesWithCount' => true,
					'legendFormatterAsSeriesAsCurrency' => true,
					'showDonutTotalLabels' => true,
				])
				: ''
			?>
		</div>
	</div>

</div>
