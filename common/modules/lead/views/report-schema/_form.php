<?php

use common\modules\lead\models\forms\LeadReportSchemaForm;

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadReportSchemaForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-report-schema-form">

	<?php $form = ActiveForm::begin([
		'id' => 'lead-report-schema-form',
	]); ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'placeholder')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'status_id')->dropDownList(LeadReportSchemaForm::getStatusNames(), [
		'prompt' => Yii::t('common', 'Select...'),
	]) ?>

	<?= $form->field($model, 'type_id')->dropDownList(LeadReportSchemaForm::getTypesNames(), [
		'prompt' => Yii::t('common', 'Select...'),
	]) ?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
