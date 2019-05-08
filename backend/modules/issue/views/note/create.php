<?php

use backend\modules\issue\models\IssueNoteForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model IssueNoteForm */

$this->title = 'Nowa notatka dla: ' . $model->note->issue;
$this->params['breadcrumbs'][] = ['label' => 'Notatki', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->note->issue, 'url' => ['issue/view', 'id' => $model->note->issue->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-note-create">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
