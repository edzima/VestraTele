<?php

use backend\helpers\Html;
use backend\modules\user\models\WorkerUserForm;
use backend\modules\user\widgets\UserProfileFormWidget;
use common\widgets\ActiveForm;
use common\widgets\address\AddressFormWidget;

/* @var $this yii\web\View */
/* @var $model WorkerUserForm */
?>

<div class="user-worker-form">


	<?php $form = ActiveForm::begin() ?>


	<?= $model->isCreate()
	&& $model->hasDuplicates()
		? $form->field($model, 'acceptDuplicates')->checkbox()
		: ''
	?>


	<div class="row">
		<?= !$model->isCreate()
			? $form->field($model, 'username', [
				'options' => [
					'class' => 'col-md-4',
				],
			])->textInput(['maxlength' => true])
			: ''
		?>

		<?= $form->field($model, 'email', [
			'options' => [
				'class' => 'col-md-3',
			],
		])->textInput(['maxlength' => true]) ?>

		<?= !$model->isCreate()
			? $form->field($model, 'password', [
				'options' => [
					'class' => 'col-md-3',
				],
			])->passwordInput(['maxlength' => true])
			: ''
		?>


	</div>


	<?= UserProfileFormWidget::widget([
		'model' => $model->getProfile(),
		'form' => $form,
	]) ?>


	<legend><?= Yii::t('common', 'Home address') ?></legend>
	<?= AddressFormWidget::widget([
		'form' => $form,
		'model' => $model->getHomeAddress(),
	]) ?>

	<legend><?= Yii::t('common', 'Postal address') ?></legend>
	<?= AddressFormWidget::widget([
		'form' => $form,
		'model' => $model->getPostalAddress(),
	]) ?>


	<?= !$model->isCreate()
		? $form->field($model, 'status')->label(Yii::t('backend', 'Status'))->radioList($model::getStatusNames())
		: ''
	?>

	<?= $form->field($model, 'roles')->checkboxList($model::getRolesNames()) ?>

	<?= $form->field($model, 'permissions')->checkboxList($model::getPermissionsNames()) ?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end() ?>
</div>
