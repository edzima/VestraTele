<?php

use common\helpers\Url;
use common\modules\lead\components\cost\CampaignCost;
use common\modules\lead\models\LeadCampaign;
use common\modules\lead\models\LeadDealStage;
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
		$chartData['seriesAvg'][] = $campaignCost->single_cost_value ? round($campaignCost->single_cost_value, 1) : null;

		$dealsCosts = $campaignCost->getStatusCost()->getDealsCosts([
			LeadDealStage::DEAL_STAGE_CONTRACT_SENT, LeadDealStage::DEAL_STAGE_CLOSED_WON,
		]);
		$stagesCounts = $campaignCost->getStatusCost()->getDealStagesCounts();
		$chartData['seriesDealWon'][] = $stagesCounts[LeadDealStage::DEAL_STAGE_CLOSED_WON] ?? null;
		$chartData['seriesDealContractSent'][] = $stagesCounts[LeadDealStage::DEAL_STAGE_CONTRACT_SENT] ?? null;
		$chartData['seriesDealCosts'][] = $dealsCosts ? round($dealsCosts, 1) : null;
	}
}

?>



<?= isset($chartData['seriesLeads'])
	?
	ChartsWidget::widget([
		'type' => ChartsWidget::TYPE_AREA,
		'height' => 420,
		'chart' => [
			'stacked' => true,
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
				'group' => 'leads',
			],
			[
				'name' => Yii::t('lead', 'Deal Stage: Closed Won'),
				'type' => ChartsWidget::TYPE_COLUMN,
				'data' => $chartData['seriesDealWon'],
				'group' => 'deal',
			],
			[
				'name' => Yii::t('lead', 'Deal Stage: Contract Sent'),
				'type' => ChartsWidget::TYPE_COLUMN,
				'data' => $chartData['seriesDealContractSent'],
				'group' => 'deal',
			],
			[
				'name' => Yii::t('lead', 'Cost Value (range)'),
				'data' => $chartData['seriesCosts'],
				'type' => ChartsWidget::TYPE_LINE,
				'group' => 'costs',
			],
			[
				'name' => Yii::t('lead', 'Single Lead cost Value'),
				'type' => ChartsWidget::TYPE_AREA,
				'data' => $chartData['seriesAvg'],
				'group' => 'costs',
			],
			[
				'name' => Yii::t('lead', 'Deal Deals Stages: Closed Won & Contract Sent - Costs'),
				'type' => ChartsWidget::TYPE_AREA,
				'data' => $chartData['seriesDealCosts'],
				'group' => 'deal',
			],
		],
		'options' => [
			'title' => [
				'text' => Yii::t('lead', 'Campaigns Costs'),
				'align' => 'center',
			],
			'stroke' => [
				'width' => [0, 0, 0, 2, 3, 3],
				'curve' => 'smooth',
			],

			'labels' => $chartData['labels'],
			'yaxis' => [
				[
					'seriesName' => Yii::t('lead', 'Leads'),
					'showForNullSeries' => false,
					'decimalsInFloat' => 0,
					//'title' => ['text' => Yii::t('lead', 'Leads'),],
					'opposite' => true,
					'labels' => [
						'show' => false,
					],
				],
				[
					'seriesName' => Yii::t('lead', 'Deal Stage: Closed Won'),
					'showForNullSeries' => false,
					'decimalsInFloat' => 0,
					'opposite' => true,
					'labels' => [
						'show' => false,
					],
					//		'title' => ['text' => Yii::t('lead', 'Deals Stages: Closed Won & Contract Sent'),],
				],
				[
					'seriesName' => Yii::t('lead', 'Deal Stage: Contract Sent'),
					'showForNullSeries' => false,
					'decimalsInFloat' => 0,
					'opposite' => true,
					'labels' => [
						'show' => false,
					],
					//		'title' => ['text' => Yii::t('lead', 'Deals Stages: Closed Won & Contract Sent'),],
				],
				[
					'seriesName' => Yii::t('lead', 'Cost Value (range)'),
					'showForNullSeries' => false,
					'decimalsInFloat' => 1,
					//	'max' => 5000,
					//	'title' => ['text' => Yii::t('lead', 'Cost Value (range)'),],
					'labels' => [
						'show' => false,
					],
				],
				[
					'seriesName' => Yii::t('lead', 'Single Lead cost Value'),
					'showForNullSeries' => false,
					'decimalsInFloat' => 1,
					//	'title' => ['text' => Yii::t('lead', 'Single Lead cost Value'),],
					'labels' => [
						'show' => false,
					],
				],

				[
					'seriesName' => [
						Yii::t('lead', 'Deal Deals Stages: Closed Won & Contract Sent - Costs'),
					],
					'showForNullSeries' => false,
					'decimalsInFloat' => 1,
					'opposite' => true,
					'labels' => [
						'show' => false,
					],
					//	'title' => ['text' => Yii::t('lead', 'Deals Stages: Costs'),],
				],
			],
		],
	])
	: ''
?>
