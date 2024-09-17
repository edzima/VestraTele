<?php

use common\helpers\Html;
use common\modules\lead\components\cost\CostComponent;
use common\modules\lead\models\LeadCost;
use common\modules\lead\models\searches\LeadCostSearch;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;

/** @var yii\web\View $this */
/** @var LeadCostSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var CostComponent $cost */

$this->title = Yii::t('lead', 'Lead Costs');
$this->params['breadcrumbs'][] = ['url' => ['lead/index'], 'label' => Yii::t('lead', 'Leads')];

$this->params['breadcrumbs'][] = $this->title;

?>
<div class="lead-cost-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('lead', 'Create Lead Cost'), ['create'], ['class' => 'btn btn-success']) ?>
		<?= Html::a(Yii::t('lead', 'Import FB Ads'), ['import-fb-ads'], ['class' => 'btn btn-warning']) ?>
	</p>

	<?= $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'attribute' => 'campaign_id',
				'label' => Yii::t('lead', 'Campaign'),
				'value' => function (LeadCost $data) use ($searchModel): string {
					return Html::a($data->campaign->getNameWithParent(), ['campaign/view', 'id' => $data->campaign_id]);
				},
				'format' => 'html',
				'filter' => $searchModel->getCampaignNames(),
				'filterType' => GridView::FILTER_SELECT2,
				'filterWidgetOptions' => [
					'pluginOptions' => ['allowClear' => true],
					'options' => [
						'placeholder' => $searchModel->getAttributeLabel('campaign_id'),
					],
				],
			],
			'value:currency',
			'singleLeadCostValue:currency',
			'leadsCount',
			'date_at:date',
			'created_at:datetime',
			'updated_at:datetime',
			[
				'class' => ActionColumn::class,
			],
		],
	]); ?>


</div>
