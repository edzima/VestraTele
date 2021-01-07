<?php

use common\models\issue\Issue;
use common\models\issue\IssueNote;
use common\modules\issue\widgets\IssueNoteWidget;

/* @var $this yii\web\View */
/* @var $model Issue */
/* @var $notes IssueNote[] */
/* @var $addUrl string */
/* @var $addBtn bool */
/* @var $noteOptions array */
?>


<fieldset>
	<legend>Notatki
		<button class="btn toggle pull-right" data-toggle="#notes-list">
			<i class="glyphicon glyphicon-chevron-down"></i></button>
	</legend>
	<div id="notes-list">
		<?php foreach ($notes as $note): ?>
			<?php
			$noteOptions['model'] = $note; ?>
			<?= IssueNoteWidget::widget($noteOptions) ?>
		<?php endforeach; ?>
	</div>
</fieldset>
