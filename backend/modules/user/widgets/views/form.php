<?php

use common\models\user\UserProfile;
use common\widgets\PhoneInput;
use yii\widgets\ActiveForm;

/* @var ActiveForm $form */
/* @var UserProfile $model */

?>


<?= $form->field($model, 'firstname')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'lastname')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'pesel')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'tax_office')->textInput(['maxlength' => true]) ?>

<div class="row">
	<?= $form->field($model, 'phone', ['options' => ['class' => 'col-md-2']])->widget(PhoneInput::class) ?>

	<?= $form->field($model, 'phone_2', ['options' => ['class' => 'col-md-2']])->widget(PhoneInput::class) ?>
</div>


<?= $form->field($model, 'email_hidden_in_frontend_issue')->checkbox() ?>

<?= $form->field($model, 'gender')->radioList(UserProfile::getGendersNames()) ?>


