<?php

use common\modules\lead\models\forms\MultipleAnswersForm;
use common\modules\lead\widgets\QuestionFieldWidget;
use common\widgets\ActiveForm;
use kartik\select2\Select2;

/**
 * @var MultipleAnswersForm $model
 * @var ActiveForm $form
 */

?>

<div class="report-answer-form">

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
</div>
