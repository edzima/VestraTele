<?php

use common\modules\lead\models\forms\LeadReportsForm;

/* @var $this \yii\web\View */
/* @var $model LeadReportsForm */

$this->title = Yii::t('lead', 'Lead Report: {id}', ['id' => $model->getLead()->getId()]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['/lead/lead/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Reports'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo $this->render('_multi_form', [
	'model' => $model,
]);






