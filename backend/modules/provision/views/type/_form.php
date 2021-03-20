<?php

use backend\modules\provision\models\ProvisionTypeForm;
use common\widgets\DateWidget;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ProvisionTypeForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="provision-type-form">

	<?php $form = ActiveForm::begin([
			'id' => 'provision-type-form',
		]
	); ?>

	<div class="row">

		<?= $form->field($model, 'issueUserType', ['options' => ['class' => 'col-md-3 col-lg-2']])
			->widget(Select2::class, [
				'data' => ProvisionTypeForm::getIssueUserTypesNames(),
				'options' => [
					'multiple' => false,
				],
			])
		?>

		<?= $form->field($model, 'issueRequiredUserTypes', ['options' => ['class' => 'col-md-3 col-lg-2']])
			->widget(Select2::class, [
				'data' => ProvisionTypeForm::getIssueUserTypesNames(),
				'options' => [
					'multiple' => true,
				],
			])
		?>

		<?= $form->field($model, 'name', ['options' => ['class' => 'col-md-4']])
			->textInput(['maxlength' => true])
		?>

	</div>

	<div class="row">


		<?= $form->field($model, 'issueTypesIds', ['options' => ['class' => 'col-md-6']])->widget(Select2::class, [
			'data' => ProvisionTypeForm::getIssueTypesNames(),
			'options' => [
				'multiple' => true,
			],
		])->hint(Yii::t('provision', 'Empty - all'))
		?>


		<?= $form->field($model, 'issueStagesIds', ['options' => ['class' => 'col-md-6']])->widget(Select2::class, [
			'data' => ProvisionTypeForm::getIssueStagesNames(),
			'options' => [
				'multiple' => true,
			],
		])->hint(Yii::t('provision', 'Empty - all'))
		?>


		<?= $form->field($model, 'calculationTypes', ['options' => ['class' => 'col-md-6']])->widget(Select2::class, [
			'data' => ProvisionTypeForm::getCalculationTypesNames(),
			'options' => [
				'multiple' => true,
			],
		])->hint(Yii::t('provision', 'Empty - all'))
		?>


	</div>

	<div class="row">

		<?= $form->field($model, 'value', ['options' => ['class' => 'col-md-3 col-lg-2']])->textInput() ?>

		<?= $form->field($model, 'is_percentage', ['options' => ['class' => 'col-md-2']])->checkbox()
			->hint(Yii::t('provision', 'Percent or Const'))
		?>

	</div>


	<div class="row">
		<?= $form->field($model, 'is_active', ['options' => ['class' => 'col-md-2']])->checkbox() ?>

		<?= $form->field($model, 'with_hierarchy', ['options' => ['class' => 'col-md-2']])->checkbox() ?>

		<?= $form->field($model, 'is_default', ['options' => ['class' => 'col-md-2']])->checkbox() ?>
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
