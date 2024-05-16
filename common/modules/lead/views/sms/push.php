<?php

use common\helpers\Html;
use common\modules\lead\models\LeadSmsForm;
use common\widgets\ActiveForm;
use common\widgets\DateTimeWidget;
use yii\web\View;

/* @var $this View */
/* @var $model LeadSmsForm */

$this->title = Yii::t('lead', 'Send SMS to Lead: {lead}', ['lead' => $model->getLead()->getName()]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['lead/index']];
$this->params['breadcrumbs'][] = ['label' => $model->getLead()->getName(), 'url' => ['lead/view', 'id' => $model->getLead()->getId()]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="lead-sms-push">
	<h1><?= Html::encode($this->title) ?></h1>

	<div class="lead-sms-push-form">
		<?php $form = ActiveForm::begin(['id' => 'lead-sms-push-form']) ?>

		<div class="row">
			<?= $form->field($model, 'status_id', [
				'options' => [
					'class' => 'col-md-4',
				],
			])->dropDownList(LeadSmsForm::getStatusNames()) ?>


		</div>


		<div class="row">

			<?= $form->field($model, 'phone', [
				'options' => [
					'class' => 'col-md-4',
				],
			])->textInput(['readonly' => true]) ?>


		</div>

		<div class="row">
			<?= $form->field($model, 'message', [
				'options' => [
					'class' => 'col-md-4',
				],
			])->textarea() ?>
		</div>

		<div class="row">

			<?= $form->field($model, 'withOverwrite', [
				'options' => [
					'class' => 'col-md-2',
				],
			])->checkbox() ?>

			<?= $form->field($model, 'removeSpecialCharacters', [
				'options' => [
					'class' => 'col-md-2',
				],
			])->checkbox() ?>
		</div>

		<div class="row">
			<?= $form->field($model, 'delayAt', [
				'options' => [
					'class' => 'col-md-3 col-lg-2',
				],
			])->widget(
				DateTimeWidget::class,
			) ?>

		</div>


		<div class="form-group">
			<?= Html::submitButton(Yii::t('lead', 'Send'), ['class' => 'btn btn-success']) ?>
		</div>

		<?php ActiveForm::end() ?>
	</div>
</div>
