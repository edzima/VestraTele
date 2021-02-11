<?php

use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadForm;
use common\widgets\DateTimeWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model Lead */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'status_id')->dropDownList(LeadForm::getStatusNames()) ?>

	<?= $form->field($model, 'type_id')->dropDownList(LeadForm::getTypesNames()) ?>

	<?= $form->field($model, 'date_at')->widget(DateTimeWidget::class) ?>

	<?= $form->field($model, 'source')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'data')->textarea(['rows' => 6]) ?>

	<?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'postal_code')->textInput(['maxlength' => true]) ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
