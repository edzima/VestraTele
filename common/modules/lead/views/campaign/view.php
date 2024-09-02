<?php

use common\helpers\ArrayHelper;
use common\helpers\Html;
use common\modules\lead\models\LeadCampaign;
use common\modules\lead\models\searches\LeadCampaignCostSearch;
use common\modules\lead\Module;
use common\modules\lead\widgets\chart\CampaignCostChart;
use common\modules\lead\widgets\chart\LeadStatusChart;
use common\modules\lead\widgets\chart\LeadUsersStatusChart;
use common\modules\lead\widgets\StatusDealStageDetailView;
use common\widgets\charts\ChartsWidget;
use common\widgets\GridView;
use yii\data\ActiveDataProvider;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model LeadCampaign */
/* @var $campaignCost LeadCampaignCostSearch */

$this->title = $model->name;

$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Campaigns'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);

$singleCostValue = 0;
$totalCostsValue = $model->getTotalCostSumValue();
if ($totalCostsValue) {
	$totalLeads = $model->getTotalLeadsCount();
	if ($totalLeads) {
		$singleCostValue = $totalCostsValue / $totalLeads;
	}
}

$costDataProvider = $campaignCost->getCostQueryDataProvider();
$leadsDataProvider = $campaignCost->getLeadsDataProvider();
$statusCost = $campaignCost->getStatusCost();

?>
<div class="lead-campaign-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('lead', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Module::getInstance()->allowDelete
			? Html::a(Yii::t('lead', 'Delete'), ['delete', 'id' => $model->id], [
				'class' => 'btn btn-danger',
				'data' => [
					'confirm' => Yii::t('lead', 'Are you sure you want to delete this item?'),
					'method' => 'post',
				],
			]) : '' ?>
	</p>


	<div class="row">

		<div class="col-md-5 col-lg-4">
			<?= DetailView::widget([
				'model' => $model,
				'attributes' => [
					//	'id',
					'name',
					[
						'attribute' => 'details',
						'visible' => !empty($model->details),
					],
					[
						'attribute' => 'typeName',
						'visible' => !empty($model->type),
					],
					[
						'label' => Yii::t('lead', 'Total Cost Value (total)'),
						'value' => $totalCostsValue,
						'format' => 'currency',
						'visible' => !empty($totalCostsValue),
					],
					[
						'label' => Yii::t('lead', 'Single Lead cost Value (total)'),
						'value' => $singleCostValue,
						'format' => 'currency',
						'visible' => !empty($singleCostValue),
					],
					[
						'attribute' => 'parent',
						'value' => function ($model) {
							return Html::a($model->parent->name, [
								'view', 'id' => $model->parent_id,
							]);
						},
						'format' => 'html',
						'visible' => !empty($model->parent),
						'label' => $model->parent ? $model->parent->getTypeName() : null,
					],
					[
						'attribute' => 'url',
						'format' => 'url',
						'visible' => !empty($model->url),
					],
					[
						'attribute' => 'owner',
						'visible' => !empty($model->owner),
					],
					[
						'attribute' => 'sort_index',
						'visible' => !empty($model->sort_index),
					],
					'is_active:boolean',

				],
			]) ?>
		</div>


		<div class="col-md-7 col-lg-8">
			<?= GridView::widget([
				'dataProvider' => new ActiveDataProvider([
					'query' => LeadCampaign::find()
						->andWhere(['id' => $model->getChildesIds()])
						->with('leads'),
				]),
				'showOnEmpty' => false,
				'emptyText' => '',
				'columns' => [
					[
						'attribute' => 'name',
						'format' => 'html',
						'value' => function (LeadCampaign $data) use ($campaignCost): string {
							return Html::a($data->name, [
								'view',
								'id' => $data->id,
								Html::getInputName($campaignCost, 'fromAt') => $campaignCost->fromAt,
								Html::getInputName($campaignCost, 'toAt') => $campaignCost->toAt,
							]);
						},
					],
					'typeName',
					[
						'attribute' => 'leads',
						'value' => function (LeadCampaign $data) use ($campaignCost): ?float {
							$search = $campaignCost->getOrCreateForCampaignIds([
								$data->id,
							]);
							return $search->getLeadsTotalCount();
						},
					],
					[
						'attribute' => 'totalCostSumValue',
						'format' => 'currency',
						'value' => function (LeadCampaign $data) use ($campaignCost): ?float {
							return $campaignCost->getOrCreateForCampaignIds([
								$data->id,
							])->getCostSum();
						},
					],
					[
						'attribute' => 'singleCostValue',
						'value' => function (LeadCampaign $data) use ($campaignCost): ?float {
							$search = $campaignCost->getOrCreateForCampaignIds([
								$data->id,
							]);
							$count = $search->getLeadsTotalCount();
							if ($count) {
								return $search->getCostSum() / $count;
							}
							return null;
						},
						'format' => 'currency',
						'label' => Yii::t('lead', 'Single Costs Value'),
					],
					[
						'label' => Yii::t('lead', 'Deal Contracts Costs'),
						'value' => function (LeadCampaign $data) use ($campaignCost): string {
							$search = $campaignCost->getOrCreateForCampaignIds([
								$data->id,
							]);
							return StatusDealStageDetailView::widget([
								'model' => $search->getStatusCost(),
								'options' => [
									'tag' => 'ul',
								],
								'template' => '<li>{label}: {value}</li>',
							]);
						},
						'format' => 'raw',
					],

				],

			]) ?>


		</div>
	</div>

	<div class="clearfix"></div>

	<div class="row">

		<div class="col-md-6 col-lg-4">
			<?= $this->render('_view_search', ['model' => $campaignCost,]) ?>

			<?= DetailView::widget([
				'model' => $campaignCost,
				'attributes' => array_merge([
					[
						'attribute' => 'costSum',
						'format' => 'currency',
						'label' => Yii::t('lead', 'Total Cost Value'),
					],
					[
						'attribute' => 'leadsCount',
						'value' => $leadsDataProvider->getTotalCount(),
						'label' => Yii::t('lead', 'Leads Count'),
					],
					[
						'attribute' => 'singleLeadCost',
						'value' => $campaignCost->getCostSum() && $leadsDataProvider->getTotalCount()
							? $campaignCost->getCostSum() / $leadsDataProvider->getTotalCount()
							: 0,
						'visible' => !empty($leadsDataProvider->getTotalCount()),
						'format' => 'currency',
						'label' => Yii::t('lead', 'Single Costs Value'),
					],
				],
					StatusDealStageDetailView::attributesFromStatusCost($statusCost, StatusDealStageDetailView::STAGES_CLOSED_WON_WITH_CONTRACT_SENT)
				),
			]) ?>
		</div>

		<div class="col-md-6 col-lg-4">
			<?= LeadStatusChart::widget(['statuses' => $statusCost->getStatusCounts(),]) ?>
		</div>


	</div>


	<div class="row">

		<div class="col-md-12">
			<?php
			$chartCostDayData = CampaignCostChart::costDayData($costDataProvider->query);
			?>
			<?= !empty($chartCostDayData['series'])
				? ChartsWidget::widget([
					'series' => $chartCostDayData['series'],
					'type' => ChartsWidget::TYPE_AREA,
					'height' => '400px',
					'chart' => ['stacked' => true,],
					'options' => [
						'xaxis' => ['type' => 'datetime',],
						'yaxis' => [
							[
								'seriesName' => $chartCostDayData['yAxis']['seriesNames.leads'],
								'decimalsInFloat' => 0,
								'showForNullSeries' => false,
								'title' => ['text' => Yii::t('lead', 'Leads'),],
							],
							[
								'seriesName' => $chartCostDayData['yAxis']['seriesNames.cost'],
								'decimalsInFloat' => 1,
								'showForNullSeries' => false,
								'title' => ['text' => Yii::t('lead', 'Cost'),],
							],
							[
								'seriesName' => $chartCostDayData['yAxis']['seriesNames.avg'],
								'opposite' => true,
								'showForNullSeries' => false,
								'decimalsInFloat' => 1,
								'title' => ['text' => Yii::t('lead', 'Single Lead cost Value'),],
							],
						],
						'stroke' => [
							'curve' => 'smooth',
							'width' => ArrayHelper::getColumn($chartCostDayData['series'], 'strokeWidth', false),
						],
					],
				])
				: ''
			?>
		</div>


	</div>

	<?= LeadUsersStatusChart::widget([
		'query' => $leadsDataProvider->query,
	]) ?>

	<div class="row">
		<div class="col-md-6">
			<?php
			//			GridView::widget([
			//				'dataProvider' => $costDataProvider,
			//				'caption' => Yii::t('lead', 'Costs'),
			//				'showPageSummary' => true,
			//				'columns' => [
			//					'date_at:date',
			//					[
			//						'class' => CurrencyColumn::class,
			//						'pageSummary' => true,
			//						'attribute' => 'value',
			//					],
			//					'leadsCount',
			//					'campaign',
			//					'campaign.typeName',
			//					//		'singleLeadCostValue:currency',
			//					[
			//						'class' => ActionColumn::class,
			//						'controller' => 'cost',
			//					],
			//				],
			//			])
			?>
		</div>

		<div class="col-md-6">
			<?php
			//
			//			GridView::widget([
			//				'dataProvider' => $leadsDataProvider,
			//				'caption' => Yii::t('lead', 'Leads'),
			//				'columns' => [
			//					[
			//						'attribute' => 'name',
			//						'format' => 'html',
			//						'value' => function (ActiveLead $model) {
			//							return Html::a($model->getName(), Url::leadView($model->getId()));
			//						},
			//					],
			//					'statusName',
			//					'sourceName',
			//					'date_at:datetime',
			//					'cost_value:currency',
			//				],
			//			])
			?>
		</div>
	</div>


</div>
