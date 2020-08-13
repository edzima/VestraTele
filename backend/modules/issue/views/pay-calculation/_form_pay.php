<?php

use common\models\issue\IssuePay;
use common\widgets\DateTimeWidget;
use kartik\number\NumberControl;
use yii\bootstrap\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model IssuePay */
/* @var $id int */
/* @var $index int */
/* @var $withBorder bool */
/* @var $form ActiveForm */

$options = [
	'class' => 'pay-wrapper',
];

if ($withBorder) {
	Html::addCssClass($options, 'border');
	Html::addCssClass($options, $model->isPayed() ? 'border-green' : 'border-red');
}

?>

<fieldset>
	<?= Html::beginTag('div', $options) ?>
	<legend>Rata nr: <?= $index + 1 ?></legend>
	<?php $disabled = $model->isPayed(); ?>

	<div class="issue-pay">
		<div class="row">
			<?= $form->field($model, "[$id]value", ['options' => ['class' => 'col-md-4 col-lg-2']])->widget(NumberControl::class, [

			]) ?>

			<?= $form->field($model, "[$id]vat", ['options' => ['class' => 'col-md-1 col-lg-1']])->widget(NumberControl::class) ?>
			<?= $form->field($model, "[$id]deadline_at", ['options' => ['class' => 'col-md-4 col-lg-2']])
				->widget(DateTimeWidget::class,
					[
						'phpDatetimeFormat' => 'yyyy-MM-dd',
						'options' => [
							//	'disabled' => $disabled,
						],
					]) ?>
			<?= $form->field($model, "[$id]pay_at", ['options' => ['class' => 'col-md-4 col-lg-2']])
				->widget(DateTimeWidget::class,
					[
						'phpDatetimeFormat' => 'yyyy-MM-dd',
						'options' => [
							//	'disabled' => $disabled,
						],
					])
			?>

		</div>
	</div>

	<?= Html::endTag('div') ?>
</fieldset>
