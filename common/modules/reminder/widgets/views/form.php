<?php

use common\helpers\Html;
use common\modules\reminder\models\ReminderForm;
use common\widgets\ActiveForm;
use common\widgets\DateTimeWidget;
use yii\web\View;

/* @var $this View */
/* @var $model ReminderForm */
/* @var $options array */
/* @var $users null|array */
?>


<div class="reminder-form">

	<?php $form = ActiveForm::begin($options); ?>

	<?= $users !== null ?
		$form->field($model, 'user_id')->dropDownList($users, [
			'prompt' => Yii::t('common', 'Without User - For All Users.'),
		])
		: ''
	?>


	<?= $form->field($model, 'priority')->dropDownList($model::getPriorityNames()) ?>

	<?= $form->field($model, 'date_at')->widget(DateTimeWidget::class) ?>

	<?= $form->field($model, 'details')->textarea(['maxlength' => true]) ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('common', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
