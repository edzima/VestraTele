<?php

use backend\modules\issue\models\IssueStageForm;

/* @var $this yii\web\View */
/* @var $model IssueStageForm */

$this->title = Yii::t('backend', 'Update Issue Stage: {name}', [
	'name' => $model->getModel()->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issues'), 'url' => ['issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Stages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->name, 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="issue-stage-update">

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
