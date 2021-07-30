<?php

use common\models\issue\IssuePay;
use common\widgets\DateWidget;
use kartik\number\NumberControl;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model IssuePay */
/* @var $form ActiveForm */
?>

<div class="issue-pay-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">

		<?= $form->field($model, 'value', ['options' => ['class' => 'col-md-2']])
			->widget(NumberControl::class)
		?>

		<?= $form->field($model, 'vat', ['options' => ['class' => 'col-md-1']])
			->widget(NumberControl::class)
		?>

		<?= $form->field($model, 'deadline_at', ['options' => ['class' => 'col-md-2']])
			->widget(DateWidget::class)
		?>
		<?= $form->field($model, 'pay_at', ['options' => ['class' => 'col-md-2']])
			->widget(DateWidget::class)
		?>

	</div>
	<div class="row">
		<?= $form->field($model, 'transfer_type', ['options' => ['class' => 'col-md-2']])
			->dropDownList(IssuePay::getTransfersTypesNames())
		?>
		<?= $form->field($model, 'status', ['options' => ['class' => 'col-md-2']])
			->dropDownList(IssuePay::getStatusNames(), [
				'prompt' => Yii::t('common', 'Status...'),
			])
		?>
	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
