<?php

use backend\modules\provision\models\ProvisionTypeForm;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ProvisionTypeForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="provision-type-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">


		<?= $form->field($model, 'name', ['options' => ['class' => 'col-md-6']])->textInput(['maxlength' => true]) ?>


		<?php // $form->field($model, 'date_from')->textInput() ?>

		<?php // $form->field($model, 'date_to')->textInput() ?>

	</div>
	<div class="row">


		<?= $form->field($model, 'value', ['options' => ['class' => 'col-md-1']])->textInput() ?>

		<?= $form->field($model, 'is_percentage', ['options' => ['class' => 'col-md-1']])->checkbox() ?>


	</div>
	<div class="row">

		<?= $form->field($model, 'rolesIds', ['options' => ['class' => 'col-md-3']])->widget(Select2::class, [
			'data' => ProvisionTypeForm::getRolesNames(),
			'options' => [
				'multiple' => true,
			],
		]) ?>

		<?= $form->field($model, 'typesIds', ['options' => ['class' => 'col-md-3']])->widget(Select2::class, [
			'data' => ProvisionTypeForm::getTypesNames(),
			'options' => [
				'multiple' => true,
			],
		]) ?>


	</div>

	<div class="row">


		<?= $form->field($model, 'only_with_tele', ['options' => ['class' => 'col-md-1']])->checkbox() ?>

		<?= $form->field($model, 'is_default', ['options' => ['class' => 'col-md-1']])->checkbox() ?>
	</div>
</div>


<div class="form-group">
	<?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>

