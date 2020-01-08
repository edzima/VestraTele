<?php

use backend\modules\issue\models\IssueProvisionUsersForm;
use backend\modules\issue\models\PayCalculationForm;
use common\widgets\DateTimeWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model PayCalculationForm */
/* @var $provisionModel IssueProvisionUsersForm */
/* @var $form yii\widgets\ActiveForm */
$pay = $model->getPayCalculation();
?>

<div class="issue-pay-calculation-form">

	<?php $form = ActiveForm::begin(); ?>
	<div class="row">


		<?= $form->field($pay, 'status', ['options' => ['class' => 'col-md-6']])->dropDownList(PayCalculationForm::getStatusNames()) ?>

		<?= $form->field($model, 'payAt', ['options' => ['class' => 'col-md-6 hidden']])
			->widget(DateTimeWidget::class,
				[
					'phpDatetimeFormat' => 'yyyy-MM-dd',
					'clientOptions' => [
						'allowInputToggle' => true,
						'sideBySide' => true,
						'widgetPositioning' => [
							'horizontal' => 'auto',
							'vertical' => 'auto',
						],
					],
					'options' => [
						'readonly' => $model->isDisallowChangePays(),
					],
				]) ?>

	</div>
	<div class="row">
		<?= $form->field($model, 'value', ['options' => ['class' => 'col-md-6']])->textInput(['maxlength' => true]) ?>

		<?= $form->field($pay, 'pay_type', ['options' => ['class' => 'col-md-6']])->dropDownList(PayCalculationForm::getPaysTypesNames()) ?>
	</div>

	<?= $form->field($pay, 'details')->textarea(['rows' => 2]) ?>

	<div class="row">
		<?= $form->field($model, 'payParts', ['options' => ['class' => 'col-md-6']])
			->textInput([
				'maxlength' => true,
				'readonly' => $model->isDisallowChangePays(),
			]) ?>

		<?= $form->field($model, 'firstBillDate', ['options' => ['class' => 'col-md-6']])
			->widget(DateTimeWidget::class,
				[
					'phpDatetimeFormat' => 'yyyy-MM-dd',
					'clientOptions' => [
						'allowInputToggle' => true,
						'sideBySide' => true,
						'widgetPositioning' => [
							'horizontal' => 'auto',
							'vertical' => 'auto',
						],
					],
					'options' => [
						'readonly' => $model->isDisallowChangePays(),
					],
				]) ?>
	</div>

	<div class="form-group">
		<?= Html::submitButton('Generuj', [
			'id' => 'generate-btn',
			'class' => 'btn btn-primary' . ($model->payParts < 2 ? ' hide' : ''),
			'name' => PayCalculationForm::GENERATE_NAME,
		]) ?>
		<?= Html::submitButton('Zapisz', ['id' => 'save-btn', 'class' => 'btn btn-success']) ?>
	</div>

	<h3 class="<?= $model->payParts < 2 ? 'hide' : '' ?>">Proponowane płatności</h3>
	<?php
	$i = 0;
	foreach ($model->getPays() as $pay) {
		echo $this->render('_form_pay', [
			'form' => $form,
			'index' => $i++,
			'model' => $pay,
			'hide' => $model->payParts < 2,
			'showTransferType' => !$model->isCreateForm(),
		]);
	}
	?>


	<?= $this->render('_provision_form', [
		'model' => $provisionModel,
		'form' => $form,
	]) ?>

	<?php ActiveForm::end(); ?>

</div>

<script>
	document.addEventListener('DOMContentLoaded', function () {
		let payPartsInput = document.getElementById('paycalculationform-payparts');
		let generatBtn = document.getElementById('generate-btn');
		let statusInput = document.getElementById('issuepaycalculation-status');
		let payAtField = document.getElementsByClassName('field-paycalculationform-payat')[0];

		function parseStatusInput() {
			if (parseInt(statusInput.value) === 100) {
				payAtField.classList.remove('hidden');
			} else {
				payAtField.classList.add('hidden');
			}
		}


		parseStatusInput();

		statusInput.addEventListener('change', parseStatusInput);


		payPartsInput.addEventListener('change', function () {
			if (payPartsInput.value > 1) {
				generatBtn.classList.remove('hide');
			} else {
				generatBtn.classList.add('hide');
			}
		});


	})
</script>
