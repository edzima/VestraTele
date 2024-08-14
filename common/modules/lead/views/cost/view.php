<?php

use common\modules\lead\models\LeadCost;
use common\modules\lead\widgets\LeadStatusChart;
use common\widgets\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var LeadCost $model */

$this->title = $model->getName();
$this->params['breadcrumbs'][] = ['url' => ['lead/index'], 'label' => Yii::t('lead', 'Leads')];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead Costs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="lead-cost-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('lead', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

		<?= Html::a(Yii::t('lead', 'Recalculate'), ['recalculate', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>

		<?= Html::a(Yii::t('lead', 'Delete'), ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => Yii::t('lead', 'Are you sure you want to delete this item?'),
				'method' => 'post',
			],
		]) ?>
	</p>

	<div class="row">
		<div class="col-md-6">

			<?= DetailView::widget([
				'model' => $model,
				'attributes' => [
					[
						'attribute' => 'campaign',
						'value' => Html::a($model->campaign, ['campaign/view', 'id' => $model->campaign_id]),
						'format' => 'html',
					],
					'value:currency',
					'singleLeadCostValue:currency',
					'date_at:date',
					'created_at:datetime',
					'updated_at:datetime',
				],
			]) ?>

			<?= LeadStatusChart::widget([
				'query' => $model->getLeads(),
			]) ?>

		</div>

		<div class="col-md-6">

			<?= GridView::widget([
				'dataProvider' => new ActiveDataProvider(['query' => $model->getLeads(),]),
				'columns' => [
					'sourceName',
					'name',
					'date_at',
					'cost_value:currency',
					'statusName',
				],
			]) ?>
		</div>
	</div>


</div>
