<?php

use common\modules\lead\models\forms\LeadReportSchemaForm;

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

	<?= $form->field($model, 'status_ids')->checkboxList($model::getStatusNames()) ?>

	<?= $form->field($model, 'types_ids')->checkboxList($model::getTypesNames()) ?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
