<?php

use common\helpers\Html;
use common\modules\reminder\models\ReminderForm;
use common\widgets\ActiveForm;
use common\widgets\DateTimeWidget;
use yii\web\View;

/* @var $this View */
/* @var $model ReminderForm */
/* @var $options array */
/* @var $usersItems array */
/* @var $usersOptions array|Closure */
/* @var $fieldsOptions array */
?>


<div class="reminder-form">

	<?php $form = ActiveForm::begin($options); ?>

	<div class="row">
		<?= $form->field(
			$model,
			'date_at',
			$fieldsOptions['date_at'] ?? []
		)->widget(DateTimeWidget::class, [
			'containerOptions' => [
				'id' => 'reminder-date-widget',
			],
		])
		?>


		<?= $form->field(
			$model,
			'priority',
			$fieldsOptions['priority'] ?? []
		)->dropDownList($model::getPriorityNames()) ?>


		<?= is_array($usersOptions) && !empty($usersItems)
			? $form->field(
				$model,
				'user_id', [
				'options' => [
					'class' => ['col-md-3 col-lg-2'],
				],
			])->dropDownList($usersItems, $usersOptions)
			: ''
		?>

		<?= is_callable($usersOptions)
			? call_user_func($usersOptions, $model, $form)
			: ''
		?>
	</div>

	<div class="row">
		<?= $form->field(
			$model,
			'details',
			$fieldsOptions['details'] ?? []
		)->textarea(['maxlength' => true]) ?>
	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('common', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
