<?php

use common\helpers\Html;
use common\modules\lead\models\forms\ReportForm;
use common\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this \yii\web\View */
/* @var $model ReportForm */

?>


<div class="lead-report-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'status_id')->dropDownList(ReportForm::getStatusNames()) ?>


	<?php foreach ($model->getAnswersModels() as $id => $answer): ?>
		<?= $form->field($answer, "[$id]question_id")
			->hiddenInput()
			->label(false)
		?>

		<?= $form->field($answer, "[$id]answer")
			->textInput(['placeholder' => $answer->getQuestion()->placeholder])
			->label($answer->getQuestion()->name)
		?>

	<?php endforeach; ?>

	

	<?= $form->field($model, 'details')->textarea() ?>

	<?= $form->field($model, 'closedQuestions')->widget(Select2::class, [
		'data' => $model->getClosedQuestionsData(),
		'options' => [
			'multiple' => true,
		],
	])
	?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
