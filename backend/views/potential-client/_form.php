<?php

use common\helpers\Html;
use common\models\PotentialClient;
use common\widgets\ActiveForm;
use common\widgets\address\CitySimcInputWidget;
use common\widgets\DateWidget;
use common\widgets\PhoneInput;

/** @var yii\web\View $this */
/** @var PotentialClient $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="potential-client-form">

	<?php $form = ActiveForm::begin([
		'id' => 'form-potential-client',
	]); ?>


	<div class="row">

		<?= $form->field($model, 'firstname', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->textInput(['maxlength' => true]) ?>

		<?= $form->field($model, 'lastname', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->textInput(['maxlength' => true]) ?>

		<?= $form->field($model, 'birthday', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
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


	</div>

	<div class="row">
		<?= $form->field($model, 'phone', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->widget(PhoneInput::class) ?>

		<?= $form->field($model, 'city_id', [
			'options' => [
				'class' => 'col-md-5 col-lg-4',
			],
		])->widget(CitySimcInputWidget::class) ?>


	</div>

	<?= $form->field($model, 'status')->dropDownList(PotentialClient::getStatusesNames()) ?>


	<div class="row">
		<?= $form->field($model, 'details', [
			'options' => [
				'class' => 'col-md-6 col-lg-4',
			],
		])->textarea(['rows' => 6]) ?>
	</div>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
