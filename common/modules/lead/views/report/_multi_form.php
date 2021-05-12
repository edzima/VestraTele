<?php

use common\modules\lead\models\forms\LeadReportsForm;
use kartik\select2\Select2;
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

	<?php foreach ($model->getModels() as $reportForm): ?>
		<?= $form->field($reportForm, "[$reportForm->schema_id]schema_id")->hiddenInput()->label(false) ?>

		<?php
		if ($reportForm->isTextField()) {
			echo $form->field($reportForm, "[$reportForm->schema_id]details")
				->textInput(['placeholder' => $reportForm->getSchema()->placeholder]);
		} else {
			$dropdownItems[$reportForm->schema_id] = $reportForm->getSchema()->name;
		}
		?>
	<?php endforeach; ?>
	<?= $form->field($model, 'selectedSchemas')->widget(Select2::class, [
		'data' => $dropdownItems,
		'options' => [
			'multiple' => true,
		],
	])
	?>

	<?= $form->field($model, 'details')->textarea() ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
