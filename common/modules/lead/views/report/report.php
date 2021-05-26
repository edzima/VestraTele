<?php

use common\modules\lead\models\forms\ReportForm;

/* @var $this \yii\web\View */
/* @var $model ReportForm */

$this->title = Yii::t('lead', 'Create Report: {id}', ['id' => $model->getLead()->getId()]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = ['label' => $model->getLead()->getId(), 'url' => ['lead/view', 'id' => $model->getLead()->getId()]];
$this->params['breadcrumbs'][] = $this->title;

?>

<?= $this->render('_form', [
	'model' => $model,
]) ?>






