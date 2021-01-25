<?php

use backend\modules\user\models\WorkerUserForm;
use backend\modules\user\widgets\UserProfileFormWidget;
use common\widgets\address\AddressFormWidget;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model WorkerUserForm */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = Yii::t('backend', 'Create worker');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Workers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="user-create user-worker-create">

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

	<?= $form->field($model, 'roles')->checkboxList($model::getRolesNames()) ?>

	<?= $form->field($model, 'permissions')->checkboxList($model::getPermissionsNames()) ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Create'), ['class' => 'btn btn-primary']) ?>
	</div>


	<?php ActiveForm::end() ?>


</div>
