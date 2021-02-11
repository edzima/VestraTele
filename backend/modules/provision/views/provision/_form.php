<?php

use backend\modules\provision\models\ProvisionForm;
use kartik\number\NumberControl;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ProvisionForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="provision-form">

	<?php $form = ActiveForm::begin(); ?>
	<div class="row">
		<?= $form->field($model, 'percent', ['options' => ['class' => 'col-xs-3 col-md-1']])->widget(NumberControl::class) ?>

		<?= $form->field($model, 'hide_on_report')->checkbox() ?>
	</div>
	<div class="form-group">
		<?= Html::submitButton('Zapisz', ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
