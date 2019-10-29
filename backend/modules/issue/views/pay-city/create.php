<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\issue\IssuePayCity */

$this->title = 'Ustal teminy płatności dla miejscowości';
$this->params['breadcrumbs'][] = ['label' => 'Rozliczenia', 'url' => ['pay-calculation/index']];
$this->params['breadcrumbs'][] = ['label' => 'Terminy', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-pay-city-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
