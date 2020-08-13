<?php

use backend\modules\issue\models\PayCalculationForm;
use common\widgets\DateTimeWidget;
use kartik\number\NumberControl;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model PayCalculationForm */
/* @var $form ActiveForm */
?>

<div class="issue-pay-calculation-form">

	<?php $form = ActiveForm::begin(); ?>
	<div class="row">

		<?= $form->field($model, 'providerType', ['options' => ['class' => 'col-md-3 col-lg-3']])->dropDownList($model->getProvidersNames()) ?>

		<?= $form->field($model, 'type', ['options' => ['class' => 'col-md-2 col-lg-2']])->dropDownList(PayCalculationForm::getTypesNames()) ?>

	</div>
	<div class="row">
		<?= $form->field($model, 'value', ['options' => ['class' => 'col-md-2']])->widget(NumberControl::class) ?>

		<?= $form->field($model, 'vat', ['options' => ['class' => 'col-md-1']])->widget(NumberControl::class) ?>

		<?= $form->field($model, 'payTransferType', ['options' => ['class' => 'col-md-3 col-lg-2']])->dropDownList(PayCalculationForm::getPaysTransferTypesNames()) ?>


	</div>


	<div class="row">


		<?= $form->field($model, 'paymentAt', ['options' => ['class' => 'col-md-3 col-lg-2']])
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
				]) ?>


		<?= $form->field($model, 'deadlineAt', ['options' => ['class' => 'col-md-3 col-lg-2']])
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
				]) ?>

		<?= $form->field($model, 'paysCount', ['options' => ['class' => 'col-md-2 col-lg-1']])
			->textInput([
				'maxlength' => true,
				'disabled' => $model->isPayed(),
			]) ?>


	</div>


	<div class="pays-wrapper">


		<div class="form-group">
			<?= Html::submitButton('Generuj', [
				'id' => 'generate-btn',
				'class' => 'btn btn-primary',
				'name' => PayCalculationForm::GENERATE_NAME,
			]) ?>
		</div>


		<?php if ($model->hasManyPays()): ?>

			<h3>Płatności</h3>


			<?php
			$i = 0;
			foreach ($model->getPays() as $index => $pay) {
				echo $this->render('_form_pay', [
					'form' => $form,
					'model' => $pay,
					'id' => $pay->id ?? $index,
					'index' => $i++,
					'withBorder' => !$model->isCreateForm(),
				]);
			}
			?>

		<?php endif; ?>

	</div>
	<div class="form-group">
		<?= Html::submitButton('Zapisz', ['id' => 'save-btn', 'class' => 'btn btn-success']) ?>
	</div>


	<?php ActiveForm::end(); ?>

</div>

<script>
	document.addEventListener('DOMContentLoaded', function () {
		let paysCountInput = document.getElementById('paycalculationform-payscount');
		let generatBtn = document.getElementById('generate-btn');
		let paysWrapper = document.getElementsByClassName('pays-wrapper')[0];

		function parsePaysCountInput() {
			const count = parseInt(paysCountInput.value);
			if (count > 1) {
				generatBtn.classList.remove('hide');
				paysWrapper.classList.remove('hide');
			} else {
				generatBtn.classList.add('hide');
				paysWrapper.classList.add('hide');
			}
		}


		parsePaysCountInput();

		paysCountInput.addEventListener('change', parsePaysCountInput);


	})
</script>
