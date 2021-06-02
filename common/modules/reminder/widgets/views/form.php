<?php

use common\helpers\Html;
use common\modules\reminder\models\ReminderForm;
use common\widgets\ActiveForm;
use common\widgets\DateTimeWidget;

/* @var $this \yii\web\View */
/* @var $model ReminderForm */

?>


<div class="reminder-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'priority')->dropDownList($model::getPriorityNames()) ?>

	<?= $form->field($model, 'date_at')->widget(DateTimeWidget::class) ?>

	<?= $form->field($model, 'details')->textarea(['maxlength' => true]) ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('common', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
