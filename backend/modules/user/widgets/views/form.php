<?php

use common\models\user\UserProfile;
use common\widgets\DateWidget;
use common\widgets\PhoneInput;
use yii\widgets\ActiveForm;

/* @var ActiveForm $form */
/* @var UserProfile $model */

?>

<div class="user-profile-form">

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

		<?= $form->field($model, 'pesel', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->textInput(['maxlength' => true]) ?>

		<?= $form->field($model, 'tax_office', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->textInput(['maxlength' => true]) ?>
	</div>


	<div class="row">
		<?= $form->field($model, 'phone', ['options' => ['class' => 'col-md-2 col-lg-2']])->widget(PhoneInput::class) ?>

		<?= $form->field($model, 'phone_2', ['options' => ['class' => 'col-md-2 col-lg-2']])->widget(PhoneInput::class) ?>
	</div>


	<?= $form->field($model, 'email_hidden_in_frontend_issue')->checkbox() ?>

	<?= $form->field($model, 'gender')->radioList(UserProfile::getGendersNames()) ?>


</div>
