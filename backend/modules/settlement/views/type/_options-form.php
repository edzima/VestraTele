<?php

use common\models\settlement\SettlementTypeOptions;
use common\widgets\ActiveForm;
use kartik\number\NumberControl;

/** @var yii\web\View $this */
/** @var SettlementTypeOptions $model */
/** @var ActiveForm $form */
?>

<div class="settlement-type-options-form">


	<?= $form->field($model, 'default_value')->widget(
		NumberControl::class
	) ?>


	<?= $form->field($model, 'vat')->widget(
		NumberControl::class
	) ?>

	<?= $form->field($model, 'provider_type')->dropDownList($model->providersTypesNames()) ?>


	<?= $form->field($model, 'deadline_range')
		->dropDownList($model->deadlineRangesNames(), [
			'prompt' => Yii::t('common', 'Select...'),
		]) ?>

</div>
