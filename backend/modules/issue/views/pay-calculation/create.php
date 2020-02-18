<?php

use backend\helpers\Url;
use backend\modules\issue\models\IssueProvisionUsersForm;
use backend\modules\issue\models\PayCalculationForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model PayCalculationForm */
/* @var $provisionModel IssueProvisionUsersForm */

$this->params['breadcrumbs'][] = ['label' => 'Sprawy', 'url' => ['issue/index']];
$this->params['breadcrumbs'][] = ['label' => $model->getIssue(), 'url' => Url::issueView($model->getIssue()->id)];
$this->title = 'Dodaj rozliczenie: ' . $model->getIssue();
$this->params['breadcrumbs'][] = ['label' => 'Rozliczenia', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-pay-calculation-create">


	<h1>Rozliczenie: <?= Html::a(
			$model->getIssue(),
			Url::issueView($model->getIssue()->id),
			['target' => '_blank']) ?>
	</h1>

	<?= $model->getIssue()->isSpa() ? $this->render('_pay_city_details', [
		'model' => $model->getPayCityDetails(),
	]) : '' ?>

	<?= $this->render('_form', [
		'model' => $model,
		'provisionModel' => $provisionModel,
	]) ?>


</div>
