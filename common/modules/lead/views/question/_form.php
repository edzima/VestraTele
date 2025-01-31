<?php

use common\modules\lead\models\forms\LeadQuestionForm;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadQuestionForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-question-form">

	<?php $form = ActiveForm::begin([
		'id' => 'lead-question-form',
	]); ?>

	<?= $form->field($model, 'type')->dropDownList(LeadQuestionForm::getTypesNames()) ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'placeholder')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'values')->widget(Select2::class, [
		'pluginOptions' => [
			'tags' => true,
			'multiple' => true,
		],
	]) ?>

	<?= $form->field($model, 'order')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'is_active')->checkbox() ?>

	<?= $form->field($model, 'is_required')->checkbox() ?>

	<?= $form->field($model, 'show_in_grid')->checkbox() ?>

	<?= $form->field($model, 'type_id')->dropDownList(LeadQuestionForm::getLeadTypesNames(), ['prompt' => Yii::t('common', 'Select...'),]) ?>

	<?= $form->field($model, 'status_id')->dropDownList(LeadQuestionForm::getLeadStatusNames(), ['prompt' => Yii::t('common', 'Select...'),]) ?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
