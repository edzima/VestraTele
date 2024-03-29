<?php

use common\helpers\Html;
use common\modules\reminder\widgets\ReminderGridWidget;
use lo\widgets\modal\ModalAjax;

/* @var $this yii\web\View */
/* @var $createUrl string|null */
/* @var $pjaxId string */
/* @var $controller string */
/* @var $gridOptions array */
?>


<div class="reminder-grid-modal">
	<?= $createUrl
		? ModalAjax::widget([
			'id' => 'createReminder',
			'header' => Yii::t('common', 'Create Reminder'),
			'toggleButton' => [
				'label' => Html::icon('calendar'),
				'class' => 'btn btn-warning create-btn',
				'title' => Yii::t('common', 'Create Reminder'),
				'aria-label' => Yii::t('common', 'Create Reminder'),
			],
			'url' => $createUrl,
			'ajaxSubmit' => true,
			'autoClose' => true,
			'pjaxContainer' => '#' . $pjaxId,
		])
		: ''
	?>


	<?= ModalAjax::widget([
		'id' => 'reminderGridModal',
		'selector' => 'a[data-modal-link]',
		'ajaxSubmit' => true,
		'autoClose' => true,
		'pjaxContainer' => '#' . $pjaxId,
	]); ?>


	<?= ReminderGridWidget::widget($gridOptions
	) ?>
</div>


