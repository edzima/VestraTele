<?php

use backend\modules\settlement\models\CalculationForm;
use common\modules\issue\widgets\IssueMessagesFormWidget;
use common\widgets\ActiveForm;
use common\widgets\DateWidget;
use kartik\number\NumberControl;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model CalculationForm */
/* @var $form ActiveForm */

?>

<div class="settlement-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">

		<?= $form->field($model, 'providerType', ['options' => ['class' => 'col-md-3 col-lg-2']])->dropDownList($model->getProvidersNames()) ?>

		<?= $form->field($model, 'type', ['options' => ['class' => 'col-md-2 col-lg-2']])->dropDownList(CalculationForm::getTypesNames()) ?>

	</div>
	<div class="row">
		<?= $form->field($model, 'value', ['options' => ['class' => 'col-xs-9 col-md-2 col-lg-2']])->widget(NumberControl::class) ?>

		<?= $model->getModel()->isNewRecord
			? $form->field($model, 'vat', ['options' => ['class' => 'col-xs-3 col-md-1']])->widget(NumberControl::class)
			: '' ?>

	</div>


	<div class="row">
		<?= $model->getModel()->isNewRecord || $model->getModel()->getPaysCount() < 2
			? $form->field($model, 'payment_at', ['options' => ['class' => 'col-md-3 col-lg-2']])
				->widget(DateWidget::class)
			. $form->field($model, 'deadline_at', ['options' => ['class' => 'col-md-3 col-lg-2']])
				->widget(DateWidget::class)
			: ''
		?>
	</div>

	<div class="row">
		<?= $form->field($model, 'costs_ids', ['options' => ['class' => 'col-md-6 col-lg-4']])
			->widget(Select2::class, [
					'data' => $model->getCostsData(),
					'options' => [
						'multiple' => true,
						'placeholder' => $model->getAttributeLabel('costs_ids'),
					],
				]
			)
		?>
	</div>

	<?php if ($model->getMessagesModel()): ?>
		<?= IssueMessagesFormWidget::widget([
			'form' => $form,
			'model' => $model->getMessagesModel(),
		]) ?>
	<?php endif; ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['id' => 'save-btn', 'class' => 'btn btn-success']) ?>
	</div>


	<?php ActiveForm::end(); ?>

</div>


