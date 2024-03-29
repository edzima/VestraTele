<?php

use common\modules\lead\models\LeadDialer;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model LeadDialer */

$this->title = $model->type->name . ' - ' . $model->lead->getName();

$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = ['label' => $model->lead->getName(), 'url' => ['lead/view', 'id' => $model->lead_id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Dialers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="lead-dialer-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('lead', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('lead', 'Delete'), ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => Yii::t('lead', 'Are you sure you want to delete this item?'),
				'method' => 'post',
			],
		]) ?>
	</p>

	<div class="row">
		<div class="col-md-5">


			<?= DetailView::widget([
				'model' => $model,
				'attributes' => [
					'id',
					'lead_id',
					'type.name',
					'statusName',
					'priorityName',
					'last_at:datetime',
					'created_at:datetime',
					'updated_at:datetime',
				],
			]) ?>

		</div>

		<div class="col-md-4">
			<?= DetailView::widget([
				'model' => $model->getDialer(),
				'attributes' => [
					[
						'value' => $model->getDialerStatusName(),
						'label' => Yii::t('lead', 'Status'),
					],
					[
						'attribute' => 'origin',
						'format' => 'tel',
						'label' => Yii::t('lead', 'Origin'),
					],
					[
						'attribute' => 'destination',
						'label' => Yii::t('lead', 'Destination'),
					],
				],
			]) ?>

		</div>

		<div class="col-md-3">
			<?= DetailView::widget([
				'model' => $model->getConfig(),
			]) ?>
		</div>


	</div>


</div>
