<?php

use common\models\issue\IssueClaim;
use common\widgets\ActiveForm;
use common\widgets\DateWidget;
use kartik\number\NumberControl;
use kartik\select2\Select2;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model IssueClaim */
/* @var $form yii\widgets\ActiveForm|null */
/* @var $onlyField bool */

?>

<div class="issue-claim-form">

	<?php if (!$onlyField): ?>
		<?php $form = ActiveForm::begin(); ?>
	<?php endif; ?>


	<div class="row">

		<?= $model->scenario !== IssueClaim::SCENARIO_TYPE
			? $form->field($model, 'type', [
				'options' => [
					'class' => 'col-md-2',
				],
			])->dropDownList(IssueClaim::getTypesNames())
			: ''
		?>

		<?= $form->field($model, 'entity_responsible_id', [
			'options' => [
				'class' => 'col-md-2',
			],
		])->widget(Select2::class, [
			'data' => IssueClaim::getEntityResponsibleNames(),
		])
		?>

		<?= $form->field($model, 'date', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->widget(DateWidget::class) ?>




		<?= $form->field($model, 'trying_value', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->widget(NumberControl::class) ?>

		<?= $form->field($model, 'percent_value', [
			'options' => [
				'class' => 'col-md-2 col-lg-1',
			],
		])->widget(NumberControl::class) ?>


		<?= $form->field($model, 'obtained_value', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->widget(NumberControl::class) ?>


	</div>


	<?= $form->field($model, 'details')->textarea(['maxlength' => true]) ?>


	<?php if (!$onlyField): ?>
		<div class="form-group">
			<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
		</div>

		<?php ActiveForm::end(); ?>

	<?php endif; ?>

</div>
