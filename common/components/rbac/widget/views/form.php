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

<div class="model-actions-form">
	<div class="row">

		<div class="col-md-4">
			<?php $form = ActiveForm::begin(); ?>

			<?php foreach ($model->getModels() as $index => $model): ?>


				<legend><?= Html::encode($model->getName()) ?></legend>

				<?= $form->field($model, "[$index]roles")->widget(Select2::class, [
					'data' => $model->getRolesNames(),
					'options' => [
						'multiple' => true,
					],
				]) ?>

				<?= $form->field($model, "[$index]permissions")->widget(Select2::class, [
					'data' => $model->getPermissionsNames(),
					'options' => [
						'multiple' => true,
					],
				]) ?>

				<?= $form->field($model, "[$index]usersIds")->widget(Select2::class, [
					'data' => $model->getUsersNames(),
					'options' => [
						'multiple' => true,
					],
				]) ?>

				<?= $form->field($model, "[$index]description")->textInput() ?>

			<?php endforeach; ?>
		</div>
	</div>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>



