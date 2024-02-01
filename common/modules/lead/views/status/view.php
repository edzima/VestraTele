<?php

use common\modules\lead\models\LeadStatus;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model LeadStatus */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead Statuses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="lead-status-view">

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

	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			'id',
			'name',
			'description',
			[
				'attribute' => 'hours_deadline',
				'visible' => !empty($model->hours_deadline),
			],
			[
				'attribute' => 'hours_deadline_warning',
				'visible' => !empty($model->hours_deadline_warning),
			],
			[
				'attribute' => 'statuses',
				'format' => 'html',
				'value' => function () use ($model): string {
					return Html::ul($model->getStatusesNames());
				},
				'visible' => !empty($model->getStatusesIds()),
			],
			'marketStatusName',
			'not_for_dialer:boolean',
			'show_report_in_lead_index:boolean',
			'short_report:boolean',
			'sort_index',
			[
				'attribute' => 'calendar_background',
				'visible' => $model->calendar_background !== null,
				'contentOptions' => [
					'style' => [
						'background-color' => $model->calendar_background,
					],
				],
			],
		],
	]) ?>

</div>
