<?php

use backend\helpers\Html;
use backend\modules\user\models\UserForm;
use backend\modules\user\widgets\UserProfileFormWidget;
use common\widgets\ActiveForm;
use common\widgets\address\AddressFormWidget;

/* @var $model UserForm */

?>

<?php $form = ActiveForm::begin(['id' => 'user-create-form']) ?>

<?= $model->scenario === UserForm::SCENARIO_CREATE
&& $model->hasDuplicates()
	? $form->field($model, 'acceptDuplicates')->checkbox()
	: ''
?>

<?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
<?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
<?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>


<?= UserProfileFormWidget::widget([
	'model' => $model->getProfile(),
	'form' => $form,
]) ?>

<?= AddressFormWidget::widget([
	'form' => $form,
	'model' => $model->getHomeAddress(),
]) ?>

<?= $form->field($model, 'status')->radioList($model::getStatusNames()) ?>

<?= $form->field($model, 'roles')->checkboxList($model::getRolesNames()) ?>

<?= $form->field($model, 'permissions')->checkboxList($model::getPermissionsNames()) ?>

<div class="form-group">
	<?= Html::submitButton(Yii::t('backend', 'Create'), ['class' => 'btn btn-primary']) ?>
</div>


<?php ActiveForm::end() ?>
