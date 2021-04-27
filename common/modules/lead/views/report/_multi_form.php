<?php

use common\modules\lead\models\forms\LeadReportsForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadReportsForm */
/* @var $form yii\widgets\ActiveForm */

$dropdownItems = [];
?>

<div class="lead-report-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'status_id')->dropDownList(LeadReportsForm::getStatusNames()) ?>

	<?php foreach ($model->getModels() as $index => $reportForm): ?>
		<?= $form->field($reportForm, "[$index]schema_id")->hiddenInput()->label(false) ?>

		<?php
		if ($reportForm->getSchema()->placeholder) {
			echo $form->field($reportForm, "[$index]details")
				->textInput()
				->label($reportForm->getSchema()->name);
		} else {
			$dropdownItems[$reportForm->schema_id] = $reportForm->getSchema()->name;
		}
		?>
	<? endforeach; ?>
	<?= $form->field($model, 'reports')->widget(\kartik\select2\Select2::class, [
		'data' => $dropdownItems,
		'options' => [
			'multiple' => true,
		],
	])
	?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
