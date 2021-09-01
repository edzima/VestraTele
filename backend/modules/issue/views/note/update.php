<?php

use backend\helpers\Breadcrumbs;
use backend\modules\issue\models\IssueNoteForm;

/* @var $this yii\web\View */
/* @var $model IssueNoteForm */
$this->title = Yii::t('issue', 'Update Issue Note: {title}', [
	'title' => $model->title,
]);
$this->params['breadcrumbs'] = Breadcrumbs::issue($model->getModel()->getIssueModel());
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issue Notes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<div class="issue-note-update">

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
