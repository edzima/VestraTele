<?php

use common\modules\lead\models\forms\LeadDialerForm;
use kartik\number\NumberControl;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadDialerForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-dialer-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">

		<?= !$model->getModel()->isNewRecord ?
			$form->field($model, 'status', [
				'options' => [
					'class' => 'col-md-3',
				],
			])
				->dropDownList(LeadDialerForm::getStatusesNames())
			: ''
		?>

		<?= $model->scenario === LeadDialerForm::SCENARIO_MULTIPLE
			? Html::hiddenInput('leadsIds', implode(',', $model->leadId))
			: $form->field($model, 'leadId', [
				'options' => [
					'class' => 'col-md-1',
				],
			])->textInput()
		?>

		<?= $form->field($model, 'typeId', [
			'options' => [
				'class' => 'col-md-3',
			],
		])
			->dropDownList(LeadDialerForm::getTypesNames())
		?>

		<?= $form->field($model, 'priority', [
			'options' => [
				'class' => 'col-md-1',
			],
		])
			->dropDownList(LeadDialerForm::getPriorityNames())
		?>


		<?= $form->field($model, 'destination', [
			'options' => [
				'class' => 'col-md-2',
			],
		])
			->textInput()->hint(Yii::t('lead', 'When empty: from Source'))
		?>

	</div>


	<div class="row">
		<fieldset>
			<legend class="col-md-12"><?= Yii::t('lead', 'Dialer Config') ?></legend>


			<?= $form->field($model, 'nextCallInterval', [
				'options' => [
					'class' => 'col-md-1',
				],
			])
				->widget(NumberControl::class)->hint(
					Yii::t('lead', 'Seconds')
				) ?>

			<?= $form->field($model, 'dailyAttemptsLimit', [
				'options' => [
					'class' => 'col-md-1',
				],
			])
				->widget(NumberControl::class) ?>

			<?= $form->field($model, 'globallyAttemptsLimit', [
				'options' => [
					'class' => 'col-md-1',
				],
			])
				->widget(NumberControl::class) ?>

		</fieldset>
	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
