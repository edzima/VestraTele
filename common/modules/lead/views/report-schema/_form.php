<?php

use common\modules\lead\models\forms\LeadReportSchemaForm;

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadReportSchemaForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-report-schema-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'placeholder')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'status_ids')->widget(Select2::class, [
		'data' => $model::getStatusNames(),
		'options' => [
			'placeholder' => $model->getAttributeLabel('status_ids'),
			'multiple' => true,
		],
	])->hint(Yii::t('lead', 'Allow empty to not use filter.')) ?>


	<?= $form->field($model, 'types_ids')->widget(Select2::class, [
		'data' => $model::getTypesNames(),
		'options' => [
			'placeholder' => $model->getAttributeLabel('types_ids'),
			'multiple' => true,
		],
	])->hint(Yii::t('lead', 'Allow empty to not use filter.')) ?>



	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
