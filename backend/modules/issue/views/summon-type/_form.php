<?php

use backend\modules\issue\models\SummonTypeForm;
use kartik\color\ColorInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model SummonTypeForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="summon-type-form">

	<?php $form = ActiveForm::begin(); ?>


	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'short_name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'calendar_background')->widget(
		ColorInput::class
	) ?>

	<?= $this->render('_options_form', [
		'form' => $form,
		'model' => $model->getOptions(),
	]) ?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
