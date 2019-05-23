<?php

use common\models\issue\Issue;
use common\modules\issue\widgets\IssueNoteWidget;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model Issue */
/* @var $addBtn bool */
/* @var $noteOptions array */
?>


<fieldset>
	<legend>Notatki
		<?php if ($addBtn): ?>
			<?= Html::a(
				'<i class="fa fa-plus"></i>',
				['note/create', 'issueId' => $model->id], [
				'class' => 'btn btn-xs btn-success',
			]) ?>
		<?php endif; ?>
		<button class="btn toggle pull-right" data-toggle="#notes-list">
			<i class="glyphicon glyphicon-chevron-down"></i></button>
	</legend>
	<div id="notes-list">
		<?php foreach ($model->issueNotes as $note): ?>
			<?php
			$noteOptions['model'] = $note; ?>
			<?= IssueNoteWidget::widget($noteOptions) ?>
		<?php endforeach; ?>
	</div>
</fieldset>