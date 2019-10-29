<?php

use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\issue\IssuePayCity */

$this->title = $model->city;
$this->params['breadcrumbs'][] = ['label' => 'Rozliczenia', 'url' => ['pay-calculation/index']];
$this->params['breadcrumbs'][] = ['label' => 'Terminy', 'url' => ['index']];

$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="issue-pay-city-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a('Edytuj', ['update', 'id' => $model->city_id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Usuń', ['delete', 'id' => $model->city_id], [
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
			'bank_transfer_at:monthDay',
			'direct_at:monthDay',
			'phone',
		],
	]) ?>

</div>
