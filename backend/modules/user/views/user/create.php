<?php

use backend\modules\user\models\UserForm;
use backend\modules\user\widgets\UserProfileFormWidget;
use common\widgets\address\AddressFormWidget;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model UserForm */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = Yii::t('backend', 'Create user');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Users'), 'url' => ['index']];

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

	<?php $form = ActiveForm::begin(['id' => 'user-create-form']) ?>

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


</div>
