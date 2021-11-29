<?php

use common\helpers\Html;
use common\models\message\IssueSmsForm;
use common\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\View;

/* @var $this View */
/* @var $model IssueSmsForm */
/* @var $formOptions array */

?>

<div class="issue-sms-push-form">
	<?php $form = ActiveForm::begin($formOptions) ?>

	<?= $model->isMultiple()
		? $form->field($model, 'phones')->widget(Select2::class, [
			'data' => $model->getPhonesData(),
			'options' => [
				'multiple' => true,
				'placeholder' => $model->getAttributeLabel('phones'),
			],
		]) : $form->field($model, 'phone')->dropDownList($model->getPhonesData()) ?>

	<?= $form->field($model, 'note_title')->textInput() ?>

	<?= $form->field($model, 'message')->textarea() ?>

	<?= $form->field($model, 'removeSpecialCharacters')->checkbox() ?>

	<?= $form->field($model, 'withOverwrite')->checkbox()->hint($model->getMessage()->getOverwriteSrc()) ?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('common', 'Send SMS'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end() ?>
</div>



