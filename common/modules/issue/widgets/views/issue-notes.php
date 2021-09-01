<?php

use common\models\issue\Issue;
use common\models\issue\IssueNote;
use common\modules\issue\widgets\IssueNoteWidget;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model Issue */
/* @var $notes IssueNote[] */
/* @var $noteOptions array */
/* @var $title string */
/* @var $id string */
?>


<fieldset>
	<legend><?= $title ?>
		<button class="btn toggle pull-right" data-toggle="#<?= Html::encode($id) ?>>">
			<i class="glyphicon glyphicon-chevron-down"></i></button>
	</legend>
	<div id="<?= Html::encode($id) ?>">
		<?php foreach ($notes as $note): ?>
			<?php
			$noteOptions['model'] = $note; ?>
			<?= IssueNoteWidget::widget($noteOptions) ?>
		<?php endforeach; ?>
	</div>
</fieldset>
