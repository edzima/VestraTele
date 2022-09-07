<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\reminder\models\searches\ReminderSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reminder-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<?= $form->field($model, 'id') ?>

	<?= $form->field($model, 'priority') ?>

	<?= $form->field($model, 'created_at') ?>

	<?= $form->field($model, 'updated_at') ?>

	<?= $form->field($model, 'date_at') ?>

	<?php // echo $form->field($model, 'details') ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('reminder', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::resetButton(Yii::t('reminder', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
