<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\address\State;

/* @var $this yii\web\View */
/* @var $model State */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="state-form">

	<?php $form = ActiveForm::begin(); ?>


	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<div class="form-group">
		<?= Html::submitButton($model->isNewRecord ? 'Dodaj' : 'Zapisz', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
