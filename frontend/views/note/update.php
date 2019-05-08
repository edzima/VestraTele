<?php

use backend\modules\issue\models\IssueNoteForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model IssueNoteForm */
$this->title = 'Edytuj notatke: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Sprawy', 'url' => ['/issue/index']];
$this->params['breadcrumbs'][] = ['label' => $model->note->issue, 'url' => ['/issue/view', 'id' => $model->note->issue->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-note-update">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
