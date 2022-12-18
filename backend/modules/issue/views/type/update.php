<?php

use backend\modules\issue\models\IssueTypeForm;

/* @var $this yii\web\View */
/* @var $model IssueTypeForm */

$this->title = Yii::t('backend', 'Update Issue Type: {name}', [
	'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issues'), 'url' => ['issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Issue Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="issue-type-update">

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
