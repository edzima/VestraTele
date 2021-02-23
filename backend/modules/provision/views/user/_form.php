<?php

use backend\modules\provision\models\ProvisionUserForm;
use common\widgets\DateWidget;
use kartik\number\NumberControl;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ProvisionUserForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="provision-type-form">

	<?php $form = ActiveForm::begin([
			'id' => 'provision-user-form',
		]
	); ?>

	<div class="row">

		<?= !$model->isSelf()
			? $form->field($model, 'to_user_id', ['options' => ['class' => 'col-md-6 col-lg-4']])
				->widget(Select2::class, [
					'data' => ProvisionUserForm::getUserNames(),
				])
			: ''
		?>

		<?= !$model->isSelf()
			? $form->field($model, 'from_user_id', ['options' => ['class' => 'col-md-6 col-lg-4']])
				->widget(Select2::class, [
					'data' => ProvisionUserForm::getUserNames(),
				])
			: ''
		?>


	</div>

	<div class="row">

		<?= $form->field($model, 'type_id', ['options' => ['class' => 'col-md-4']])->widget(Select2::class, [
			'data' => ProvisionUserForm::getTypesNames(),
		]) ?>

		<?= $form->field($model, 'value', ['options' => ['class' => 'col-md-2']])->widget(NumberControl::class) ?>

	</div>


	<div class="row">
		<?= $form->field($model, 'from_at', ['options' => ['class' => 'col-md-3']])->widget(DateWidget::class) ?>

		<?= $form->field($model, 'to_at', ['options' => ['class' => 'col-md-3']])->widget(DateWidget::class) ?>
	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
