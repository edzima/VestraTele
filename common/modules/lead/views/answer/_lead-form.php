<?php

use common\helpers\Html;
use common\modules\lead\models\forms\MultipleAnswersForm;
use common\modules\lead\widgets\QuestionFieldWidget;
use common\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model MultipleAnswersForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-answer-form">

	<?php $form = ActiveForm::begin(); ?>

	<?php foreach ($model->getAnswersModels() as $id => $answer): ?>
		<?= $form->field($answer, "[$id]answer")
			->widget(QuestionFieldWidget::class, [
				'question' => $answer->getQuestion(),
			])
		?>

	<?php endforeach; ?>

	<?= $form->field($model, 'tags')->widget(Select2::class, [
		'data' => $model->getTagsData(),
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
