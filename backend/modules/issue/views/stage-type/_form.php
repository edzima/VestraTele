<?php

use backend\helpers\Html;
use backend\modules\issue\models\StageTypeForm;
use common\widgets\ActiveForm;
use kartik\color\ColorInput;
use kartik\select2\Select2;

/* @var StageTypeForm $model */
/* @var $withType bool */
/* @var $withStage bool */

?>

<div class="issue-stage-type-form">


	<?php

	$form = ActiveForm::begin(['id' => 'issue-stage-type-form']);
	?>

	<div class="row">


		<?= $withType
			? $form->field($model, 'type_id', [
				'options' => [
					'class' => 'col-md-6',
				],
			])->widget(Select2::class, [
				'data' => $model->getTypesNames(),
			])
			: ''
		?>

		<?= $withStage
			? $form->field($model, 'stage_id', [
				'options' => [
					'class' => 'col-md-6',
				],
			])->widget(Select2::class, [
				'data' => $model->getStagesNames(),
			]) : '' ?>
	</div>

	<div class="row">
		<?= $form->field($model, 'days_reminder', [
			'options' => [
				'class' => 'col-md-1',
			],
		])->textInput(['maxlength' => true]) ?>

		<?= $form->field($model, 'days_reminder_second', [
			'options' => [
				'class' => 'col-md-1',
			],
		])->textInput(['maxlength' => true]) ?>


		<?= $form->field($model, 'days_reminder_third', [
			'options' => [
				'class' => 'col-md-1',
			],
		])->textInput(['maxlength' => true]) ?>

		<?= $form->field($model, 'days_reminder_fourth', [
			'options' => [
				'class' => 'col-md-1',
			],
		])->textInput(['maxlength' => true]) ?>

		<?= $form->field($model, 'days_reminder_fifth', [
			'options' => [
				'class' => 'col-md-1',
			],
		])->textInput(['maxlength' => true]) ?>
	</div>


	<?= $form->field($model, 'calendar_background')->widget(
		ColorInput::class
	) ?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

