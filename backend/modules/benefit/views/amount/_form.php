<?php

use common\models\benefit\BenefitAmount;
use common\widgets\DateTimeWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model BenefitAmount */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="benefit-amount-form">

	<?php $form = ActiveForm::begin(); ?>
	<div class="row">
		<?= $form->field($model, 'type', [
			'options' => [
				'class' => 'col-md-6',

			],
		])->dropDownList(BenefitAmount::getTypesNames()) ?>

		<?= $form->field($model, 'value', [
			'options' => [
				'class' => 'col-md-6',

			],
		])->textInput(['maxlength' => true]) ?>
	</div>

	<div class="row">

		<?= $form->field($model, 'from_at', ['options' => ['class' => 'col-md-6']])
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

		<?= $form->field($model, 'to_at', ['options' => ['class' => 'col-md-6']])
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


	</div>


	<div class="form-group">
		<?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
