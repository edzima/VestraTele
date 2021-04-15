<?php

use common\modules\lead\models\forms\LeadForm;
use common\widgets\DateTimeWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">
		<?= $form->field($model, 'source_id', ['options' => ['class' => 'col-md-4']])->dropDownList(LeadForm::getSourcesNames()) ?>

		<?= $form->field($model, 'campaign_id', ['options' => ['class' => 'col-md-4']])->dropDownList(LeadForm::getCampaignsNames()) ?>

		<?= $form->field($model, 'status_id', ['options' => ['class' => 'col-md-4']])->dropDownList(LeadForm::getStatusNames()) ?>
	</div>


	<?= $form->field($model, 'datetime')->widget(DateTimeWidget::class, [
		'phpDatetimeFormat' => 'yyyy-MM-dd HH:mm:ss',
	]) ?>

	<?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'postal_code')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'data')->textarea(['rows' => 6]) ?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
