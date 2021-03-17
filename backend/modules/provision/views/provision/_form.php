<?php

use backend\modules\provision\models\ProvisionUpdateForm;
use kartik\number\NumberControl;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ProvisionUpdateForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="provision-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'hide_on_report')->checkbox() ?>

	<div class="row">

		<?= $form->field($model, 'value', ['options' => ['class' => 'col-xs-3 col-md-2']])->widget(NumberControl::class) ?>

		<?= $form->field($model, 'percent', ['options' => ['class' => 'col-xs-3 col-md-2']])->widget(NumberControl::class) ?>

	</div>
	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
