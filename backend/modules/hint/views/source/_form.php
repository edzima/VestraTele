<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\hint\HintSource */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hint-source-form">

	<?php $form = ActiveForm::begin([
		'id' => 'hint-source-form',
	]); ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'short_name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'is_active')->checkbox() ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
