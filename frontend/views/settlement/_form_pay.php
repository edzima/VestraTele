<?php

use common\models\settlement\PayForm;
use common\widgets\DateWidget;
use kartik\number\NumberControl;
use yii\bootstrap\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model PayForm */
/* @var $id int */
/* @var $index int */
/* @var $form ActiveForm */

$options = [
	'class' => 'pay-wrapper',
];

?>

<fieldset>
	<?= Html::beginTag('div', $options) ?>
	<legend>Rata nr: <?= $index + 1 ?></legend>

	<div class="pay-form">
		<div class="row">
			<?= $form->field($model, "[$id]value", ['options' => ['class' => 'col-md-4 col-lg-2']])
				->widget(NumberControl::class)
			?>
			<?= $form->field($model, "[$id]payment_at", ['options' => ['class' => 'col-md-4 col-lg-2']])
				->widget(DateWidget::class)
			?>
			<?= $form->field($model, "[$id]deadline_at", ['options' => ['class' => 'col-md-4 col-lg-2']])
				->widget(DateWidget::class)
			?>
		</div>
	</div>

	<?= Html::endTag('div') ?>
</fieldset>
