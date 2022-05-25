<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\issue\IssueTag */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="issue-tag-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'is_active')->checkbox() ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('issue', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
