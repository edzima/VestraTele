<?php

use common\components\rbac\form\ActionsAccessForm;
use common\helpers\Html;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;

/**
 * @var ActiveForm|null $form
 * @var ActionsAccessForm $model
 */
?>

<div class="single-action-access-form">
	<div class="row">

		<div class="col-md-4">
			<?php $form = ActiveForm::begin(); ?>


			<legend><?= Html::encode($model->getName()) ?></legend>

			<?= $form->field($model, "roles")->widget(Select2::class, [
				'data' => $model->getRolesNames(),
				'options' => [
					'multiple' => true,
				],
			]) ?>

			<?= $form->field($model, "permissions")->widget(Select2::class, [
				'data' => $model->getPermissionsNames(),
				'options' => [
					'multiple' => true,
				],
			]) ?>

			<?= $form->field($model, "usersIds")->widget(Select2::class, [
				'data' => $model->getUsersNames(),
				'options' => [
					'multiple' => true,
				],
			]) ?>

		</div>
	</div>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>



