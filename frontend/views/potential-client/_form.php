<?php

use common\models\PotentialClient;
use common\widgets\address\CitySimcInputWidget;
use common\widgets\DateWidget;
use common\widgets\PhoneInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model PotentialClient */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="potential-client-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">
		<?= $form->field($model, 'firstname', [
			'options' => [
				'class' => 'col-md-3',
			],
		])->textInput(['maxlength' => true]) ?>

		<?= $form->field($model, 'lastname', [
			'options' => [
				'class' => 'col-md-3',
			],
		])->textInput(['maxlength' => true]) ?>

		<?= $form->field($model, 'birthday', [
			'options' => [
				'class' => 'col-md-2',
			],
		])->widget(DateWidget::class, [
			'clientOptions' => [
				'allowInputToggle' => true,
				'sideBySide' => true,
				'viewMode' => 'years',
				'widgetPositioning' => [
					'horizontal' => 'auto',
					'vertical' => 'auto',
				],
			],
		]) ?>

		<?= $form->field($model, 'phone', [
			'options' => [
				'class' => 'col-md-2',
			],
		])->widget(PhoneInput::class) ?>

	</div>

	<?= $form->field($model, 'city_id')->widget(CitySimcInputWidget::class) ?>

	<?= $form->field($model, 'status')->dropDownList(PotentialClient::getStatusesNames()) ?>

	<?= $form->field($model, 'details')->textarea(['rows' => 4]) ?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('common', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
