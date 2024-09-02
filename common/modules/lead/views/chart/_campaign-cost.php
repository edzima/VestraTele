<?php

use common\helpers\Url;
use common\modules\lead\components\cost\CampaignCost;
use common\modules\lead\models\LeadCampaign;
use common\modules\lead\models\searches\LeadChartSearch;
use common\widgets\charts\ChartsWidget;
use yii\helpers\Json;
use yii\web\JsExpression;

/* @var $model LeadChartSearch */
/* @var $data CampaignCost[] */

$campaignsData = $model->getLeadCampaignsCount();

usort($data, function (CampaignCost $a, CampaignCost $b) {
	return $b->sum <=> $a->sum;
});

$chartData = [];
if (count($campaignsData) > 1) {

	$campaignsCostData = $model->getCampaignCost();

	$campaigns = LeadCampaign::find()
		->andWhere(['id' => array_keys($campaignsData)])
		->with('parent.parent')
		->indexBy('id')
		->all();

	foreach ($data as $campaignCost) {
		$campaign = $campaigns[$campaignCost->campaign_id];
		$name = $campaign->name;
		if ($campaign->parent) {
			$name .= ' (' . $campaign->parent->name . ')';
		}
		$url = Url::to([
			'campaign/view',
			'id' => $campaignCost->campaign_id,
			'fromAt' => $model->from_at,
			'toAt' => $model->to_at,
		]);
		$chartData['labels'][] = $name;
		$chartData['url'][] = $url;

		$count = count($campaignCost->leads_ids);
		$cost = $campaignCost->sum;
		$chartData['seriesLeads'][] = $count;
		$chartData['seriesCosts'][] = $cost;
		$chartData['seriesAvg'][] = $campaignCost->single_cost_value ? round($campaignCost->single_cost_value, 2) : null;
	}
}

?>



<?= isset($chartData['seriesLeads'])
	?
	ChartsWidget::widget([
		'type' => ChartsWidget::TYPE_AREA,
		'height' => 420,
		'chart' => [
			'events' => [
				'dataPointSelection' => new JsExpression("function(event, chartContext, opts) {
								const index = opts.dataPointIndex;
								const urls = " . Json::encode($chartData['url']) . ";
								const url = urls[index];
								console.log(index);
								if(url){
									window.open(urls[index]);
								}
                            }"),
			],
		],
		'series' => [
			[
				'name' => Yii::t('lead', 'Leads'),
				'type' => ChartsWidget::TYPE_COLUMN,
				'data' => $chartData['seriesLeads'],
			],
			[
				'name' => Yii::t('lead', 'Total Cost Value (total)'),
				'data' => $chartData['seriesCosts'],
				'type' => ChartsWidget::TYPE_LINE,
			],
			[
				'name' => Yii::t('lead', 'Single Lead cost Value'),
				'type' => ChartsWidget::TYPE_AREA,
				'data' => $chartData['seriesAvg'],
			],
		],
		'options' => [
			'title' => [
				'text' => Yii::t('lead', 'Campaigns Costs'),
				'align' => 'center',
			],
			'stroke' => [
				'width' => [0, 2, 3],
				'curve' => 'smooth',
			],

			'labels' => $chartData['labels'],
			'yaxis' => [
				[
					'seriesName' => Yii::t('lead', 'Leads'),
					'showForNullSeries' => false,
					'decimalsInFloat' => 0,
					'title' => ['text' => Yii::t('lead', 'Leads'),],
					'opposite' => true,
				],
				[
					'seriesName' => Yii::t('lead', 'Total Cost Value (total)'),
					'showForNullSeries' => false,
					'decimalsInFloat' => 2,
					'title' => ['text' => Yii::t('lead', 'Total Cost Value (total)'),],
				],
				[
					'seriesName' => Yii::t('lead', 'Single Lead cost Value'),
					'showForNullSeries' => false,
					'decimalsInFloat' => 2,
					'title' => ['text' => Yii::t('lead', 'Single Lead cost Value'),],
				],
			],
		],
	])
	: ''
?>
