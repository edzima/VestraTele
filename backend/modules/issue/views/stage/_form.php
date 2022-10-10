<?php

use backend\modules\issue\models\IssueStage;
use common\models\issue\IssueType;
use kartik\color\ColorInput;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model IssueStage */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="issue-stage-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'short_name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'posi')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'typesIds')->widget(Select2::class, [
		'data' => IssueType::getTypesNames(),
		'options' => [
			'multiple' => true,
		],
	]) ?>

	<?= $form->field($model, 'days_reminder')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'calendar_background')->widget(
		ColorInput::class
	) ?>


	<div class="form-group">
		<?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
