<?php

use backend\modules\issue\models\IssueNoteForm;

/* @var $this yii\web\View */
/* @var $model IssueNoteForm */

$this->title = Yii::t('backend', 'Create note {typeName} for: {issue}', [
	'typeName' => $model->note->typeName,
	'issue' => $model->note->issue->longId,
]);
$this->params['breadcrumbs'][] = ['label' => 'Notatki', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->note->issue, 'url' => ['issue/view', 'id' => $model->note->issue->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Create');
?>
<div class="issue-note-create">

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
