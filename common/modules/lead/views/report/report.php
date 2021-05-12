<?php

/* @var $this \yii\web\View */
/* @var $model ReportForm */

use common\modules\lead\models\forms\ReportForm;

$this->title = Yii::t('lead', 'Create Report: {id}', ['id' => $model->getLead()->getId()]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = ['label' => $model->getLead()->getId(), 'url' => ['lead/view', 'id' => $model->getLead()->getId()]];
$this->params['breadcrumbs'][] = $this->title;

//echo $this->render('_multi_form', [
//	'model' => $model,
//]);

?>

<?= $this->render('_report_form', [
	'model' => $model,
]) ?>






