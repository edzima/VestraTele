<?php

use backend\helpers\Url;
use common\models\issue\IssuePayCity;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model IssuePayCity|null */
$hasCity = $model !== null && $model->city !== null;
?>
<fieldset>
	<legend>Miejscowość wypłacająca: <?= $hasCity ? Html::a(Html::encode($model->city->name),
			Url::payCityDetails($model->city_id),
			[
				'target' => '_blank',
			]) : 'Nie ustatlono' ?>
		<button class="btn toggle pull-right" data-toggle="#pay-city-details">
			<i class="glyphicon glyphicon-chevron-down"></i></button>
	</legend>

	<?php
	if ($model === null) {
		Yii::$app->session->addFlash('warning', 'Nie ustalono miejscowości wypłat!');
	} elseif (!$model->hasTransferDate()) {
		Yii::$app->session->addFlash('warning', 'Miejscowość wypłacająca nie ma ustawionych terminów wypłat.');
	}

	?>
	<?= DetailView::widget([
		'model' => $model,
		'id' => 'pay-city-details',
		'attributes' => [
			'bank_transfer_at:monthDay',
			'direct_at:monthDay',
			[
				'attribute' => 'phone',
				'visible' => !empty($model->phone),
			],
		],
	]) ?>
</fieldset>

