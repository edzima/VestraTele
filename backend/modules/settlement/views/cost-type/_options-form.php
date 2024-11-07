<?php

use common\models\settlement\CostTypeOptions;
use common\widgets\ActiveForm;
use kartik\number\NumberControl;

/** @var yii\web\View $this */
/** @var CostTypeOptions $model */
/** @var ActiveForm $form */
?>

<div class="cost-type-options-form">

	<div class="row">
		<?= $form->field($model, 'default_value', [
			'options' => [
				'class' => 'col-md-6',
			],
		])->widget(
			NumberControl::class
		) ?>

		<?= $form->field($model, 'vat', [
			'options' => [
				'class' => 'col-md-6',
			],
		])->widget(
			NumberControl::class
		) ?>

	</div>

	<?= $form->field($model, 'user_is_required')->checkbox() ?>


	<?= $form->field($model, 'deadline_range')
		->dropDownList($model->deadlineRangesNames(), [
			'prompt' => Yii::t('common', 'Select...'),
		]) ?>

</div>
