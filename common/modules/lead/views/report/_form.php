<?php

use common\modules\lead\models\forms\LeadReportForm;
use kartik\depdrop\DepDrop;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadReportForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-report-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'status_id')->dropDownList($model::getStatusNames()) ?>

	<?= Html::hiddenInput('lead-type_id', $model->getLeadTypeID(), ['id' => 'lead-type_id']) ?>

	<?= $form->field($model, 'schema_id')->widget(DepDrop::class, [
		'type' => DepDrop::TYPE_SELECT2,
		'data' => $model->getSchemaData(),
		'pluginOptions' => [
			'url' => 'schema',
			'placeholder' => $model->getAttributeLabel('schema_id'),
			'depends' => [Html::getInputId($model, 'status_id')],
			'params' => ['lead-type_id'],
		],
	]) ?>

	<?= $form->field($model, 'details')->textarea([]) ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
