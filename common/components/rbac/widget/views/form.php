<?php

use common\components\rbac\form\ModelActionsForm;
use common\helpers\Html;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;

/**
 * @var ActiveForm|null $form
 * @var ModelActionsForm $model
 */
?>

<div class="model-access-form">
	<div class="row">

		<?php $form = ActiveForm::begin(); ?>

		<?php foreach ($model->getModels() as $index => $model): ?>

			<div class="col-md-4">

				<h3><?= Html::encode($model->getActionName()) ?></h3>


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

			</div>
		<?php endforeach; ?>

	</div>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>



