<?php

use common\modules\lead\models\forms\ReportForm;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $model ReportForm */

$this->title = Yii::t('lead', 'Create Report for Lead: {name}', ['name' => $model->getLead()->getName()]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = ['label' => $model->getLead()->getName(), 'url' => ['lead/view', 'id' => $model->getLead()->getId()]];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="lead-report-report">
	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_lead', [
		'model' => $model->getLead(),
	]) ?>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>







