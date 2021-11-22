<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\issue\SummonType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="summon-type-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'short_name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'title')->textarea(['maxlength' => true]) ?>

	<?= $form->field($model, 'term')->textInput(['maxlength' => true]) ?>


	<div class="form-group">
		<?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
