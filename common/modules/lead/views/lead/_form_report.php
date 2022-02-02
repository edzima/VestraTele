<?php

use common\modules\lead\models\forms\LeadForm;
use common\modules\lead\models\forms\ReportForm;
use common\modules\lead\widgets\ReportFormWidget;
use common\widgets\DateTimeWidget;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadForm */
/* @var $report ReportForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-form">

	<?php $form = ActiveForm::begin([
		'id' => 'lead-with-report-form',
	]); ?>

	<div class="row">
		<?= $form->field($model, 'provider', ['options' => ['class' => 'col-md-2']])->dropDownList(LeadForm::getProvidersNames(), ['prompt' => Yii::t('lead', '--- Select ---')]) ?>

		<?= $form->field($model, 'campaign_id', ['options' => ['class' => 'col-md-3']])->dropDownList($model->getCampaignsNames(), ['prompt' => Yii::t('lead', '--- Select ---')]) ?>

		<?= $form->field($model, 'status_id', ['options' => ['class' => 'col-md-3']])->dropDownList(LeadForm::getStatusNames()) ?>

		<?= $form->field($model, 'date_at', ['options' => ['class' => 'col-md-3']])->widget(DateTimeWidget::class, [
			'phpDatetimeFormat' => 'yyyy-MM-dd HH:mm:ss',
		]) ?>

	</div>


	<div class="row">

		<?= $form->field($model, 'name', ['options' => ['class' => 'col-md-4']])->textInput(['maxlength' => true]) ?>

		<?= $form->field($model, 'phone', ['options' => ['class' => 'col-md-3']])->textInput(['maxlength' => true]) ?>

		<?= $form->field($model, 'email', ['options' => ['class' => 'col-md-3']])->textInput(['maxlength' => true]) ?>

		<?= $form->field($model, 'postal_code', ['options' => ['class' => 'col-md-2']])->textInput(['maxlength' => true]) ?>
	</div>

	<div class="row">
		<?= $model->scenario !== LeadForm::SCENARIO_OWNER
			? $form->field($model, 'owner_id', ['options' => ['class' => 'col-md-3']])->widget(Select2::class, [
				'data' => LeadForm::getUsersNames(),
				'pluginOptions' => [
					'placeholder' => $model->getAttributeLabel('owner_id'),
					'allowClear' => true,
				],
			])
			: ''
		?>

		<?= $form->field($model, 'agent_id', ['options' => ['class' => 'col-md-3']])->widget(Select2::class, [
			'data' => LeadForm::getUsersNames(),
			'pluginOptions' => [
				'placeholder' => $model->getAttributeLabel('agent_id'),
				'allowClear' => true,
			],
		]) ?>
	</div>


	<?= ReportFormWidget::widget([
		'form' => $form,
		'model' => $report,
		'withSameContacts' => true,
	]) ?>


	<?php //form->field($model, 'data')->textarea(['rows' => 6]) ?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
