<?php

use backend\helpers\Html;
use backend\modules\user\models\UserForm;
use backend\modules\user\models\WorkerUserForm;
use backend\modules\user\widgets\UserProfileFormWidget;
use common\widgets\ActiveForm;
use common\widgets\address\AddressFormWidget;

/* @var $model WorkerUserForm */

?>

<?php $form = ActiveForm::begin() ?>

<?= $model->scenario === UserForm::SCENARIO_CREATE
&& $model->hasDuplicates()
	? $form->field($model, 'acceptDuplicates')->checkbox()
	: ''
?>

<?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

<?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

<?= UserProfileFormWidget::widget([
	'model' => $model->getProfile(),
	'form' => $form,
]) ?>


<?= AddressFormWidget::widget([
	'form' => $form,
	'model' => $model->getHomeAddress(),
]) ?>

<?= $form->field($model, 'roles')->checkboxList($model::getRolesNames()) ?>

<?= $form->field($model, 'permissions')->checkboxList($model::getPermissionsNames()) ?>

<div class="form-group">
	<?= Html::submitButton(Yii::t('backend', 'Create'), ['class' => 'btn btn-primary']) ?>
</div>


<?php ActiveForm::end() ?>
