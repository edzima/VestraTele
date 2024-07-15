<?php

use common\modules\lead\models\LeadCampaign;
use common\modules\lead\Module;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model LeadCampaign */

$this->title = $model->name;

$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Campaigns'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
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

	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			//	'id',
			'name',
			[
				'attribute' => 'typeName',
				'visible' => !empty($model->type),
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
			[
				'attribute' => 'details',
				'visible' => !empty($model->details),
			],
		],
	]) ?>

</div>
