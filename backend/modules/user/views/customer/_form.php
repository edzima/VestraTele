<?php

use backend\modules\user\models\CustomerUserForm;
use backend\modules\user\widgets\UserProfileFormWidget;
use common\widgets\address\AddressFormWidget;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/* @var $model CustomerUserForm */

?>


<?php $form = ActiveForm::begin(['id' => 'customer-form']) ?>

<?= !$model->getModel()->isNewRecord ? $form->field($model, 'username')->textInput(['maxlength' => true]) : '' ?>

<?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

<?= UserProfileFormWidget::widget([
	'model' => $model->getProfile(),
	'form' => $form,
]) ?>

<?= AddressFormWidget::widget([
	'form' => $form,
	'model' => $model->getHomeAddress(),
]) ?>


<div class="form-group">
	<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end() ?>
