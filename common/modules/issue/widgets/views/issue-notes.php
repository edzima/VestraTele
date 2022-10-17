<?php

use common\helpers\Html;
use common\models\issue\IssueNote;
use common\modules\issue\widgets\IssueNoteWidget;

/* @var $this yii\web\View */
/* @var $notes IssueNote[] */
/* @var $noteOptions array */
/* @var $title string */
/* @var $id string */
/* @var $frontendCount int */

$userCount = count(array_filter($notes, static function (IssueNote $note): bool {
	return $note->isUserFrontend();
}));

$stagesChangesCount = count(array_filter($notes, static function (IssueNote $note): bool {
	return $note->isForStageChange();
}));

$smsCount = count(array_filter($notes, static function (IssueNote $note): bool {
	return $note->isSms();
}))

?>


<fieldset>
	<legend>
		<?= $title ?>

		<span class="btn-group pull-right">

					<?= $smsCount
						? Html::button(Html::icon('envelope') . " $smsCount", [
							'class' => 'btn btn-sm',
							'data-toggle' => 'collapse',
							'data-target' => "#$id ." . IssueNoteWidget::getTypeKindClass(IssueNote::TYPE_SMS),
							'onClick' => 'this.classList.toggle("active");',
							'title' => Yii::t('common', 'SMS'),
							'aria-label' => Yii::t('common', 'SMS'),
						])
						: ''
					?>

					<?= $stagesChangesCount
						? Html::button(Html::icon('retweet') . " $stagesChangesCount", [
							'class' => 'btn btn-sm',
							'data-toggle' => 'collapse',
							'data-target' => "#$id ." . IssueNoteWidget::getTypeKindClass(IssueNote::TYPE_STAGE_CHANGE),
							'onClick' => 'this.classList.toggle("active");',
							'title' => Yii::t('common', 'Stage Change'),
							'aria-label' => Yii::t('common', 'Stage Change'),
						])
						: ''
					?>
					<?= $userCount
						? Html::button(Html::icon('user') . " $userCount", [
							'class' => 'btn btn-sm',
							'data-toggle' => 'collapse',
							'data-target' => "#$id ." . IssueNoteWidget::getTypeKindClass(IssueNote::TYPE_USER_FRONT),
							'onClick' => 'this.classList.toggle("active");',
							'title' => Yii::t('common', 'User Frontend'),
							'aria-label' => Yii::t('common', 'User Frontend'),
						])
						: ''
					?>
					<?= Html::button(Html::icon('chevron-down'), [
						'class' => 'btn btn-sm toggle',
						'data-toggle' => 'collapse',
						'data-target' => "#$id",
					]) ?>
		</span>

	</legend>
	<div id="<?= Html::encode($id) ?>" class="collapse in">
		<?php foreach ($notes as $note): ?>
			<?php
			$options = $noteOptions;
			$options['model'] = $note;
			?>
			<?= IssueNoteWidget::widget(array_merge($options, [
				'options' => [
					'class' => IssueNoteWidget::getTypeKindClass($note->getTypeKind()),
				],
			])) ?>
		<?php endforeach; ?>
	</div>
</fieldset>
