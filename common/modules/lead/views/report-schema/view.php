<?php

use common\modules\lead\models\LeadReportSchema;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model LeadReportSchema */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Reports'), 'url' => ['/lead/report/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Schemas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="lead-report-schema-view">

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
			'placeholder',
			'is_required:boolean',
			'show_in_grid:boolean',
			[
				'attribute' => 'type',
				'value' => $model->type->name,
				'visible' => !empty($model->type),
			],
			[
				'attribute' => 'status',
				'value' => $model->status->name,
				'visible' => !empty($model->status),
			],
		],
	]) ?>

</div>
