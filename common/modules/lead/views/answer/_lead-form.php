<?php

use common\helpers\Html;
use common\modules\lead\models\forms\MultipleAnswersForm;
use common\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model MultipleAnswersForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-answer-form">

	<?php $form = ActiveForm::begin(); ?>

	<?php foreach ($model->getAnswersModels() as $id => $answer): ?>
		<?php if ($answer->getQuestion()->is_boolean): ?>
			<?= $form->field($answer, "[$id]answer")
				->radioList(Html::booleanDropdownList())
				->label($answer->getQuestion()->name)
			?>
		<?php else: ?>
			<?= $form->field($answer, "[$id]answer")
				->textInput(['placeholder' => $answer->getQuestion()->placeholder])
				->label($answer->getQuestion()->name)
			?>
		<?php endif; ?>


	<?php endforeach; ?>

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
