<?php

use common\models\issue\SummonReminderForm;
use common\modules\reminder\widgets\ReminderFormWidget;

/* @var $this yii\web\View */
/* @var $model SummonReminderForm */

?>
<div class="reminder-form-create">

	<?= ReminderFormWidget::widget([
		'model' => $model,
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
