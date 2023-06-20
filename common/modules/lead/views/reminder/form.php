<?php

use common\modules\lead\models\forms\LeadReminderForm;
use common\modules\reminder\widgets\ReminderFormWidget;

/* @var $this yii\web\View */
/* @var $model LeadReminderForm $model */

?>
<div class="lead-reminder-form">
	<?= ReminderFormWidget::widget([
		'model' => $model,
		'users' => [
			'items' => $model->getUsersNames(),
			'prompt' => Yii::t('lead', 'For all Users in Lead.'),
		],
		'fieldsOptions' => [
			'date_at' => [
				'options' => [
					'class' => ['col-md-6'],
				],
			],
			'priority' => [
				'options' => [
					'class' => ['col-md-6'],
				],
			],
			'details' => [
				'options' => [
					'class' => ['col-md-12'],
				],
			],
		],
	]) ?>
</div>
