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
					'class' => 'col-md-3',
				],
			])->widget(Select2::class, [
				'data' => $model->getTypesNames(),
			])
			: ''
		?>

		<?= $withStage
			? $form->field($model, 'stage_id', [
				'options' => [
					'class' => 'col-md-3',
				],
			])->widget(Select2::class, [
				'data' => $model->getStagesNames(),
			]) : '' ?>
	</div>

	<div class="row">
		<?= $form->field($model, 'days_reminder', [
			'options' => [
				'class' => 'col-md-2',
			],
		])->textInput(['maxlength' => true]) ?>

		<?= $form->field($model, 'calendar_background', [
			'options' => [
				'class' => 'col-md-2',
			],
		])->widget(
			ColorInput::class
		) ?>
	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

