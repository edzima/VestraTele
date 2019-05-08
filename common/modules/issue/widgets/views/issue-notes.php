<?php

use common\models\issue\Issue;
use common\modules\issue\widgets\IssueNoteWidget;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model Issue */
/* @var $addBtn bool */
/* @var $notesOptions array */
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
		<btn class="btn toggle pull-right" data-toggle="#notes-list">
			<i class="glyphicon glyphicon-chevron-down"></i></btn>
	</legend>
	<div id="notes-list">
		<?php foreach ($model->issueNotes as $note): ?>
			<?php
			$notesOptions['model'] = $note;
			?>
			<?= IssueNoteWidget::widget($notesOptions) ?>
		<?php endforeach; ?>
	</div>
</fieldset>