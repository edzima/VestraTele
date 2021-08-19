<?php

use common\models\user\UserProfile;
use yii\widgets\ActiveForm;

/* @var ActiveForm $form */
/* @var UserProfile $model */

?>

<?= $form->field($model, 'firstname')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'lastname')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'pesel')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'tax_office')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'phone_2')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'gender')->radioList(UserProfile::getGendersNames()) ?>

