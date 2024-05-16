<?php

use common\helpers\Html;
use common\modules\credit\models\CreditClientAnalyze;
use common\widgets\ActiveForm;
use common\widgets\DateWidget;
use common\widgets\PhoneInput;
use kartik\number\NumberControl;
use kartik\select2\Select2;
use yii\web\View;

/* @var $this View */
/* @var $model CreditClientAnalyze */

?>

<div class="credit-analyze-form">
	<?php $form = ActiveForm::begin([
		'method' => 'POST',
	]) ?>

	<div class="row">

		<?= $form->field($model, 'amountOfCanceledInterestOnFuture')->hiddenInput()->label(false) ?>
		<?= $form->field($model, 'estimatedRefundAmount')->hiddenInput()->label(false) ?>
		<?= $form->field($model, 'analyzeAt')->hiddenInput()->label(false) ?>


		<?= $form->field($model, 'borrower', [
			'options' => [
				'class' => 'col-md-6',
			],
		])->textInput()
		?>

		<?= $form->field($model, 'entityResponsibleId', [
			'options' => [
				'class' => 'col-md-6',
			],
		])->widget(Select2::class, [
			'data' => $model->getEntityResponsibleNames(),
		]) ?>
	</div>
	<div class="row">
		<?= $form->field($model, 'phone', [
			'options' => [
				'class' => 'col-md-6',
			],
		])->widget(PhoneInput::class)
		?>
		<?= $form->field($model, 'email', [
			'options' => [
				'class' => 'col-md-6',
			],
		])->textInput()
		?>
	</div>

	<div class="row">


		<?= $form->field($model, 'agreement', [
			'options' => [
				'class' => 'col-md-3',
			],
		])->textInput([
			'placeholder' => Yii::t('credit', 'Agreement Number'),
		]) ?>

		<?= $form->field($model, 'agreementAt', [
			'options' => [
				'class' => 'col-md-3 col-lg-3',
			],
		])->widget(DateWidget::class) ?>

		<?= $form->field($model, 'repaymentAt', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->widget(DateWidget::class) ?>

	</div>
	<div class="row">


		<?= $form->field($model, 'totalLoanAmount', [
			'options' => [
				'class' => [
					'col-md-3 col-lg-2',
				],
			],
		])
			->widget(NumberControl::class)
		?>

		<?= $form->field($model, 'amountOfLoanGranted', [
			'options' => [
				'class' => [
					'col-md-3 col-lg-2',
				],
			],
		])
			->widget(NumberControl::class)
		?>
	</div>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('credit', 'PDF'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>
</div>
