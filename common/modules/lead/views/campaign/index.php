<?php

use common\models\user\Worker;
use common\modules\lead\models\LeadCampaign;
use common\modules\lead\models\searches\LeadCampaignSearch;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel LeadCampaignSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $visibleButtons array */

$this->title = Yii::t('lead', 'Lead Campaigns');
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = Yii::t('lead', 'Campaigns');
?>
<div class="lead-campaign-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('lead', 'Create Lead Campaign'), ['create'], ['class' => 'btn btn-success']) ?>
		<?= Yii::$app->user->can(Worker::PERMISSION_LEAD_COST)
			? Html::a(Yii::t('lead', 'Lead Costs'), ['cost/index'], ['class' => 'btn btn-primary'])
			: ''
		?>
	</p>

	<?= $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'id' => 'lead-campaign-grid',
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			//			'id',
			'name',
			[
				'attribute' => 'type',
				'value' => 'typeName',
				'filter' => LeadCampaignSearch::getTypesNames(),
				'filterType' => GridView::FILTER_SELECT2,
				'filterWidgetOptions' => [
					'pluginOptions' => [
						'placeholder' => Yii::t('lead', 'Select...'),
						'allowClear' => true,
					],
				],
			],
			[
				'attribute' => 'owner_id',
				'value' => 'owner',
				'visible' => $searchModel->scenario !== $searchModel::SCENARIO_OWNER,
				'filter' => LeadCampaignSearch::getOwnersNames(),
				'filterType' => GridView::FILTER_SELECT2,
				'filterWidgetOptions' => [
					'pluginOptions' => [
						'placeholder' => Yii::t('lead', 'Select...'),
						'allowClear' => true,
					],
				],
			],
			'is_active:boolean',
			'sort_index',
			[
				'attribute' => 'totalCostSumValue',
				'format' => 'currency',
			],
			[
				'attribute' => 'leads_count',
				'value' => function (LeadCampaign $model) {
					return $model->getTotalLeadsCount();
				},
			],
			[
				'class' => ActionColumn::class,
				'visibleButtons' => $visibleButtons,
			],
		],
	]); ?>


</div>
