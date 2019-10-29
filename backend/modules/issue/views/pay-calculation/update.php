<?php

use backend\helpers\Url;
use backend\modules\issue\models\PayCalculationForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model PayCalculationForm */

$this->title = 'Edytuj rozliczenie: ' . $model->getIssue();

$this->params['breadcrumbs'][] = ['label' => 'Sprawy', 'url' => ['issue/index']];
$this->params['breadcrumbs'][] = ['label' => $model->getIssue(), 'url' => Url::issueView($model->getIssue()->id)];
$this->params['breadcrumbs'][] = ['label' => 'Rozliczenia', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Rozliczenie: ' . $model->getIssue(), 'url' => ['view', 'id' => $model->getIssue()->id]];
$this->params['breadcrumbs'][] = 'Edycja';
?>
<div class="issue-pay-calculation-update">

	<h1>Rozliczenie: <?= Html::a(
			$model->getIssue(),
			Url::issueView($model->getIssue()->id),
			['target' => '_blank']) ?>
	</h1>

	<?= $this->render('_pay_city_details', [
		'model' => $model->getPayCityDetails(),
	]) ?>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
