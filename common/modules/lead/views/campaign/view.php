<?php

use common\helpers\Html;
use common\helpers\Url;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\LeadCampaign;
use common\modules\lead\Module;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model LeadCampaign */
/* @var $costQueryDataProvider DataProviderInterface */
/* @var $leadsDataProvider DataProviderInterface */

$this->title = $model->name;

$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Campaigns'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);

$totalCostsValue = $model->getTotalCostSumValue();
$totalLeads = $leadsDataProvider->getTotalCount();
$singleCostValue = 0;
if ($totalLeads && $totalCostsValue) {
	$singleCostValue = $totalCostsValue / $totalLeads;
}
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

		<div class="col-md-4">
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
						'label' => Yii::t('lead', 'Cost'),
						'value' => $totalCostsValue,
						'format' => 'currency',
						'visible' => !empty($totalCostsValue),
					],
					[
						'label' => Yii::t('lead', 'Single Lead cost Value'),
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
						'attribute' => 'entity_id',
						'visible' => !empty($model->entity_id),
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

		<div class="col-md-6">
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
						'value' => function (LeadCampaign $data): string {
							return Html::a($data->name, [
								'view', 'id' => $data->id,
							]);
						},
					],
					'typeName',
					[
						'attribute' => 'leads',
						'value' => function (LeadCampaign $data): int {
							return $data->getLeads()
								->count();
						},
					],
				],
			]) ?>
		</div>
	</div>

	<div class="clearfix"></div>


	<div class="row">
		<div class="col-md-6">
			<?= GridView::widget([
				'dataProvider' => $costQueryDataProvider,
				'caption' => Yii::t('lead', 'Costs'),
				'columns' => [
					'date_at:date',
					'value:currency',
					'leadsCount',
					'campaign',
					'campaign.typeName',
					//		'singleLeadCostValue:currency',
					[
						'class' => ActionColumn::class,
						'controller' => 'cost',
					],
				],
			]) ?>
		</div>

		<div class="col-md-6">
			<?= GridView::widget([
				'dataProvider' => $leadsDataProvider,
				'caption' => Yii::t('lead', 'Leads'),
				'columns' => [
					[
						'attribute' => 'name',
						'format' => 'html',
						'value' => function (ActiveLead $model) {
							return Html::a($model->getName(), Url::leadView($model->getId()));
						},
					],
					'statusName',
					'sourceName',
					'date_at:datetime',
					'cost_value:currency',
				],
			]) ?>
		</div>
	</div>


</div>
