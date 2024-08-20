<?php

/* @var $model LeadChartSearch */

use common\helpers\Url;
use common\modules\lead\models\LeadCampaign;
use common\modules\lead\models\searches\LeadChartSearch;
use common\widgets\charts\ChartsWidget;
use yii\helpers\Json;
use yii\web\JsExpression;

$campaignsData = $model->getLeadCampaignsCount();

if (count($campaignsData) > 1) {

	$campaignsCostData = $model->getCampaignCost();

	$campaigns = LeadCampaign::find()
		->andWhere(['id' => array_keys($campaignsData)])
		->with('parent.parent')
		->indexBy('id')
		->all();
	foreach ($campaignsData as $id => $count) {
		if (empty($id)) {
			$name = Yii::t('lead', 'Without Campaign');
			$url = null;
		} else {
			$campaign = $campaigns[$id];
			$name = $campaign->name;
			if ($campaign->parent) {
				$name .= ' (' . $campaign->parent->name . ')';
			}
			$url = Url::to([
				'campaign/view',
				'id' => $id,
				'fromAt' => $model->from_at,
				'toAt' => $model->to_at,
			]);
		}
		$campaignsData['url'][] = $url;

		$campaignsData['series'][] = [
			'x' => $name,
			'y' => $count,
		];

		$cost = $campaignsCostData[$id] ?? 0;
		$campaignsData['costSeries'][] = [
			'x' => $name,
			'y' => $cost ?: null,
		];
		$avg = $count && $cost
			? $cost / $count
			: null;

		$campaignsData['avgSeries'][] = [
			'x' => $name,
			'y' => round($avg, 2),
		];
	}
}

?>

<?= isset($campaignsData['series']) ?
	ChartsWidget::widget([
		'type' => ChartsWidget::TYPE_AREA,
		'height' => 420,
		'chart' => [
			'events' => [
				'dataPointSelection' => new JsExpression("function(event, chartContext, opts) {
								const index = opts.dataPointIndex;
								const urls = " . Json::encode($campaignsData['url']) . ";
								const url = urls[index];
								if(url){
									window.open(urls[index]);
								}
                            }"),
			],
		],
		'series' => [
			[
				'name' => Yii::t('lead', 'Costs'),
				'data' => $campaignsData['costSeries'],
				'type' => ChartsWidget::TYPE_LINE,
			],
			[
				'name' => Yii::t('lead', 'AVG'),
				'type' => ChartsWidget::TYPE_LINE,
				'data' => $campaignsData['avgSeries'],
			],
			[
				'name' => Yii::t('lead', 'Leads'),
				'type' => ChartsWidget::TYPE_COLUMN,
				'data' => $campaignsData['series'],
			],

		],
		'options' => [
			'title' => [
				'text' => Yii::t('lead', 'Campaigns Costs'),
				'align' => 'center',
			],
			//	'labels' => $campaignsData['labels'],

			'stroke' => [
				'width' => [3, 4, 0],
				'curve' => 'smooth',
			],

			'xaxis' =>
				[
					'type' => 'category',
				],
			'yaxis' => [
				[
					'min' => 0,
					'seriesName' => Yii::t('lead', 'Costs'),
					'showForNullSeries' => false,
					'decimalsInFloat' => 2,
					'title' => [
						'text' => Yii::t('lead', 'Costs'),
					],
				],
				[
					'min' => 0,
					'seriesName' => Yii::t('lead', 'AVG'),
					'showForNullSeries' => false,
					'decimalsInFloat' => 2,
					'title' => [
						'text' => Yii::t('lead', 'AVG'),
					],
				],
				[
					'min' => 0,
					'seriesName' => Yii::t('lead', 'Leads'),
					'showForNullSeries' => false,
					'decimalsInFloat' => 0,
					'title' => [
						'text' => Yii::t('lead', 'Leads'),
					],
					'opposite' => true,

				],

			],
		],
	])
	: ''
?>
