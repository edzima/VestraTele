<?php

use common\models\issue\IssuePay;
use common\widgets\DateTimeWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model IssuePay */
/* @var $form ActiveForm */
?>

<div class="issue-pay-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">
		<?= $form->field($model, 'deadline_at', ['options' => ['class' => 'col-md-6']])
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
				]) ?>
		<?= $form->field($model, 'pay_at', ['options' => ['class' => 'col-md-6']])
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
				]) ?>
	</div>

	<?= $form->field($model, 'vat')->textInput() ?>


	<?= $form->field($model, 'value')->textInput(['readonly' => true]) ?>

	<div class="form-group">
		<?= Html::submitButton('Zapisz', ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
