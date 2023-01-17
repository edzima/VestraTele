<?php

use backend\modules\issue\models\StageTypeForm;

/* @var $this yii\web\View */
/* @var $model StageTypeForm */

$this->title = Yii::t('backend', 'Stage: {stage} for Type: {type}', [
	'stage' => $model->getModel()->getStageName(),
	'type' => $model->getModel()->getTypeName(),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issues'), 'url' => ['issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Stages'), 'url' => ['stage/index']];
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->getStageName(), 'url' => ['stage/view', 'id' => $model->getModel()->stage_id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="issue-stage-update">
	<?= $this->render('_form', [
		'model' => $model,
		'withType' => true,
		'withStage' => true,
	]) ?>

</div>
