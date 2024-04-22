<?php

use common\modules\lead\models\LeadStatus;
use kartik\color\ColorInput;
use kartik\number\NumberControl;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadStatus */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-status-form">

	<?php $form = ActiveForm::begin([
		'id' => 'lead-status-form',
	]); ?>

	<div class="row">
		<div class="col-md-4">
			<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

			<?= $form->field($model, 'description')->textarea(['maxlength' => true]) ?>

			<?= $form->field($model, 'statusesIds')
				->widget(Select2::class, [
					'data' => LeadStatus::getNames(),
					'options' => [
						'multiple' => true,
						'placeholder' => Yii::t('lead', 'Select...'),
					],
				])->label(Yii::t('lead', 'Statuses'))
			?>

		</div>

		<div class="col-md-2">
			<?= $form->field($model, 'market_status')->dropDownList(LeadStatus::getMarketStatusesNames(), [
				'prompt' => Yii::t('lead', 'Select...'),
			]) ?>
			<?= $form->field($model, 'market_status_same_contacts')->checkbox() ?>

		</div>
		<div class="col-md-2">

			<?= $form->field($model, 'hours_deadline')->widget(NumberControl::class, [
				'maskedInputOptions' => [
					'digits' => 0,
				],
			]) ?>

			<?= $form->field($model, 'hours_deadline_warning')->widget(NumberControl::class, [
				'maskedInputOptions' => [
					'digits' => 0,
				],
			]) ?>


			<?= $form->field($model, 'calendar_background')->widget(
				ColorInput::class
			) ?>

			<?= $form->field($model, 'chart_color')->widget(
				ColorInput::class
			) ?>
		</div>

		<div class="col-md-2">

			<?= $form->field($model, 'not_for_dialer')->checkbox() ?>

			<?= $form->field($model, 'short_report')->checkbox() ?>

			<?= $form->field($model, 'show_report_in_lead_index')->checkbox() ?>

			<?= $form->field($model, 'sort_index')->textInput() ?>
		</div>
	</div>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
