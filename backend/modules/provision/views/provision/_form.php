<?php

use backend\modules\provision\models\ProvisionForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ProvisionForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="provision-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'percent')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'hide_on_report')->checkbox() ?>

	<div class="form-group">
		<?= Html::submitButton('Zapisz', ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
