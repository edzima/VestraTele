<?php

use common\modules\lead\models\LeadQuestion;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model LeadQuestion */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Questions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="lead-question-view">

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
			'typeName',
			'name',
			'placeholder',
			[
				'attribute' => 'order',
				'visible' => !empty($model->order),
			],
			'is_active:boolean',
			'is_required:boolean',
			'show_in_grid:boolean',
			[
				'attribute' => 'leadType',
				'visible' => !empty($model->leadType),
			],
			[
				'attribute' => 'leadStatus',
				'visible' => !empty($model->leadStatus),
			],
		],
	]) ?>

</div>
