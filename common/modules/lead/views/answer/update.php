<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\lead\models\LeadAnswer */

$this->title = Yii::t('lead', 'Update Lead Answer: {name}', [
	'name' => $model->report_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Lead Answers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->report_id, 'url' => ['view', 'report_id' => $model->report_id, 'question_id' => $model->question_id]];
$this->params['breadcrumbs'][] = Yii::t('lead', 'Update');
?>
<div class="lead-answer-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
