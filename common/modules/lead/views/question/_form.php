<?php

use common\modules\lead\models\forms\LeadQuestionForm;
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

	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'placeholder')->textInput(['maxlength' => true])->hint(Yii::t('lead', 'Leave empty for closed question.')) ?>

	<?= $form->field($model, 'type_id')->dropDownList(LeadQuestionForm::getTypesNames(), [
		'prompt' => Yii::t('common', 'Select...'),
	]) ?>

	<?= $form->field($model, 'status_id')->dropDownList(LeadQuestionForm::getStatusNames(), [
		'prompt' => Yii::t('common', 'Select...'),
	]) ?>

	<?= $form->field($model, 'order')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'is_active')->checkbox() ?>

	<?= $form->field($model, 'is_required')->checkbox() ?>

	<?= $form->field($model, 'is_boolean')->checkbox() ?>

	<?= $form->field($model, 'show_in_grid')->checkbox() ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
