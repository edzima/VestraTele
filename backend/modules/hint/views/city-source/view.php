<?php

use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\hint\HintCitySource */

$this->title = $model->source->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('hint', 'Hint Cities'), 'url' => ['city/index']];
$this->params['breadcrumbs'][] = ['label' => $model->hint->getCityNameWithType(), 'url' => ['city/view', 'id' => $model->hint_id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('hint', 'Hint City Sources'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="hint-city-source-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a(Yii::t('hint', 'Update'), ['update', 'source_id' => $model->source_id, 'hint_id' => $model->hint_id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('hint', 'Delete'), ['delete', 'source_id' => $model->source_id, 'hint_id' => $model->hint_id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => Yii::t('hint', 'Are you sure you want to delete this item?'),
				'method' => 'post',
			],
		]) ?>
	</p>

	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			'source.name',
			[
				'label' => Yii::t('hint', 'Hint City'),
				'value' => Html::a($model->hint->getCityNameWithType(), ['city/view', 'id' => $model->hint_id]),
				'format' => 'raw',
			],
			'phone',
			'ratingName',
			'details:ntext',
			'created_at:date',
			'updated_at:date',
		],
	]) ?>

</div>
