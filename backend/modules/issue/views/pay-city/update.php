<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\issue\IssuePayCity */

$this->title = 'Edycja: ' . $model->city;
$this->params['breadcrumbs'][] = ['label' => 'Rozliczenia', 'url' => ['pay-calculation/index']];
$this->params['breadcrumbs'][] = ['label' => 'Terminy', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->city, 'url' => ['view', 'id' => $model->city_id]];
$this->params['breadcrumbs'][] = 'Edycja';
?>
<div class="issue-pay-city-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
