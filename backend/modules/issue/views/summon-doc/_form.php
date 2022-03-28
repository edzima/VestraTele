<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\issue\SummonDoc */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="summon-doc-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
