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
$this->params['breadcrumbs'][] = ['label' => $model->getId(), 'url' => ['view', 'id' => $model->getId()]];
$this->params['breadcrumbs'][] = 'Edycja';
?>
<div class="issue-pay-calculation-update">

	<h1><?= Html::encode($this->title) ?></h1>


	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
