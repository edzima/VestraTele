<?php

use common\models\SummonTypeOptions;
use kartik\color\ColorInput;

/* @var $this yii\web\View */
/* @var $model SummonTypeOptions */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="summon-type-options-form">


	<?= $form->field($model, 'showOnTop')->checkbox() ?>

	<?= $form->field($model, 'sendEmailToContractor')->checkbox() ?>

	<?= $form->field($model, 'defaultRealizeAtFromStartAt')->checkbox() ?>

	<?= $form->field($model, 'lawsuitCalendarBackground')->widget(ColorInput::class) ?>

	<?= $form->field($model, 'term')->dropDownList(SummonTypeOptions::getTermsNames()) ?>

	<?= $form->field($model, 'formAttributes')->dropDownList(
		SummonTypeOptions::formAttributesNames(), [
		'multiple' => true,
	]) ?>

	<?= $form->field($model, 'visibleSummonFields')->dropDownList(
		SummonTypeOptions::visibleSummonAttributesNames(), [
		'multiple' => true,
	]) ?>

	<?= $form->field($model, 'requiredFields')->dropDownList(
		SummonTypeOptions::getRequiredAttributes(), [
		'multiple' => true,
	]) ?>


</div>
