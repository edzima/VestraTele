<?php

use backend\modules\issue\models\IssueTypeForm;
use common\widgets\ActiveForm;
use kartik\number\NumberControl;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model IssueTypeForm */
/* @var $form ActiveForm */
?>

<div class="issue-type-form">

	<?php $form = ActiveForm::begin([
		'id' => 'issue-type-form',
	]); ?>

	<div class="row">
		<?= $form->field($model, 'name', [
			'options' => [
				'class' => 'col-md-4',
			],
		])->textInput(['maxlength' => true]) ?>

		<?= $form->field($model, 'short_name', [
			'options' => [
				'class' => 'col-md-2',
			],
		])->textInput(['maxlength' => true]) ?>

		<?= $form->field($model, 'vat', [
			'options' => [
				'class' => 'col-md-1',
			],
		])->widget(NumberControl::class) ?>

	</div>

	<div class="row">
		<?= $form->field($model, 'parent_id', [
			'options' => [
				'class' => 'col-md-4',
			],
		])->widget(Select2::class, [
			'data' => $model->getParentsData(),
			'options' => ['placeholder' => $model->getAttributeLabel('parent_id')],
		]) ?>

		<?= $form->field($model, 'lead_source_id', [
			'options' => [
				'class' => 'col-md-2',
			],
		])->widget(NumberControl::class, [
			'maskedInputOptions' => [
				'digits' => 0,
			],
		]) ?>
	</div>


	<?= $form->field($model, 'default_show_linked_notes')->checkbox() ?>

	<?= $form->field($model, 'with_additional_date')->checkbox() ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
