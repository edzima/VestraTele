<?php

use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\benefit\BenefitAmount */

$this->title = $model->typeName . ' (' . Yii::$app->formatter->asDate($model->from_at) . ' - ' . Yii::$app->formatter->asDate($model->from_at) . ')';
$this->params['breadcrumbs'][] = ['label' => 'Benefit Amounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="benefit-amount-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Delete', ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => 'Are you sure you want to delete this item?',
				'method' => 'post',
			],
		]) ?>
	</p>

	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
			'typeName',
			'from_at:date',
			'to_at:date',
			'value',
		],
	]) ?>

</div>
