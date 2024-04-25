<?php

use common\helpers\Html;
use common\helpers\Url;
use common\modules\lead\models\LeadCost;
use common\modules\lead\models\searches\LeadCostSearch;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;

/** @var yii\web\View $this */
/** @var LeadCostSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('lead', 'Lead Costs');
$this->params['breadcrumbs'][] = ['url' => ['lead/index'], 'label' => Yii::t('lead', 'Leads')];

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-cost-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('lead', 'Create Lead Cost'), ['create'], ['class' => 'btn btn-success']) ?>
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
					return $searchModel->getCampaignNames()[$data->campaign_id];
				},
				'filter' => $searchModel->getCampaignNames(),
			],
			'value:currency',
			'date_at:date',
			'created_at:datetime',
			'updated_at:datetime',
			[
				'class' => ActionColumn::class,
				'urlCreator' => function ($action, LeadCost $model, $key, $index, $column) {
					return Url::toRoute([$action, 'id' => $model->id]);
				},
			],
		],
	]); ?>


</div>
