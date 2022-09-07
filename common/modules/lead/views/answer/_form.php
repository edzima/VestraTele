<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\lead\models\LeadAnswer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-answer-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'report_id')->textInput() ?>

	<?= $form->field($model, 'question_id')->textInput() ?>

	<?= $form->field($model, 'answer')->textInput(['maxlength' => true]) ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
