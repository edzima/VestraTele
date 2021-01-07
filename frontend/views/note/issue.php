<?php

use backend\modules\issue\models\IssueNoteForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model IssueNoteForm */

$this->title = Yii::t('common', 'Create note for issue: {id}', ['id' => $model->note->issue->longId]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Issues'), 'url' => ['/issue/index']];
$this->params['breadcrumbs'][] = ['label' => $model->note->issue, 'url' => ['/issue/view', 'id' => $model->note->issue->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-note-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
