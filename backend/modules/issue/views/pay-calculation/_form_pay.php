<?php

use common\models\issue\IssuePay;
use common\widgets\DateTimeWidget;

/* @var $this yii\web\View */
/* @var $model IssuePay */
/* @var $index int */
/* @var $hide bool */
/* @var $form yii\widgets\ActiveForm */
/* @var $showTransferType bool */
?>

<fieldset class="<?= $hide ? 'hide' : '' ?>">
	<legend>Rata nr: <?= $index + 1 ?></legend>
	<?php
	$disabled = $model->isPayed();
	?>
	<div class="issue-pay">
		<div class="row">
			<?= $form->field($model, "[$index]value", ['options' => ['class' => 'col-md-6']])->textInput(['maxlength' => true, 'disabled' => $disabled]) ?>

			<?= $form->field($model, "[$index]deadline_at", ['options' => ['class' => 'col-md-6']])
				->widget(DateTimeWidget::class,
					[
						'phpDatetimeFormat' => 'yyyy-MM-dd',
						'clientOptions' => [
							'allowInputToggle' => true,
							'sideBySide' => true,
							'widgetPositioning' => [
								'horizontal' => 'auto',
								'vertical' => 'auto',
							],
						],
						'options' => [
							'disabled' => $disabled,
						],
					]) ?>

		</div>

		<?= $form->field($model, 'transfer_type', ['options' => ['class' => !$showTransferType ? 'hide' : '']])->dropDownList(IssuePay::getTransferTypesNames()) ?>


	</div>
</fieldset>
