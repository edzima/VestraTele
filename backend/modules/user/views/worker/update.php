<?php

use backend\modules\user\models\WorkerUserForm;
use backend\modules\user\widgets\UserProfileFormWidget;
use common\widgets\address\AddressFormWidget;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model WorkerUserForm */
/* @var $form ActiveForm */

$this->title = Yii::t('backend', 'Update worker: {username}', ['username' => $model->username]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Workers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->getFullName(), 'url' => ['view', 'id' => $model->getModel()->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="user-update">

	<?php $form = ActiveForm::begin() ?>

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


	<?= $form->field($model, 'status')->label(Yii::t('backend', 'Status'))->radioList($model::getStatusNames()) ?>

	<?= $form->field($model, 'roles')->checkboxList($model::getRolesNames()) ?>

	<?= $form->field($model, 'permissions')->checkboxList($model::getPermissionsNames()) ?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end() ?>

</div>
