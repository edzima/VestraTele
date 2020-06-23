<?php

use backend\modules\address\widgets\AddressFormWidget;
use backend\modules\issue\models\MeetForm;
use common\widgets\DateTimeWidget;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model MeetForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="issue-meet-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'createdAt')
		->widget(DateTimeWidget::class)
	?>

	<?= $form->field($model, 'dateStart')
		->widget(DateTimeWidget::class)
	?>


	<?= $form->field($model, 'campaignId')->dropDownList(MeetForm::getCampaignNames()) ?>

	<?= $form->field($model, 'typeId')->dropDownList(MeetForm::getTypesNames()) ?>

	<?= $form->field($model, 'status')->dropDownList(MeetForm::getStatusNames()) ?>

	<fieldset>
		<legend>Klient</legend>
		<div class="row">
			<?= $form->field($model, 'clientName', [
				'options' => [
					'class' => 'col-md-3',
				],
			])->textInput([
			]) ?>

			<?= $form->field($model, 'clientSurname', [
				'options' => [
					'class' => 'col-md-3',
				],
			])->textInput([
			]) ?>

			<?= $form->field($model, 'phone', [
				'options' => [
					'class' => 'col-md-3',
				],
			])->textInput(['maxlength' => true]) ?>

			<?= $form->field($model, 'email', [
				'options' => [
					'class' => 'col-md-3',
				],
			])->textInput(['maxlength' => true]) ?>

		</div>
		<?= AddressFormWidget::widget([
			'form' => $form,
			'model' => $model->getAddress(),
		]) ?>
	</fieldset>


	<div class="row">
		<?= $form->field($model, 'agentId', ['options' => ['class' => 'col-md-6']])
			->widget(Select2::class, [
					'data' => MeetForm::getAgentsNames(),
					'options' => [
						'placeholder' => 'Agent',
					],
					'pluginOptions' => [
						'allowClear' => true,
					],
				]
			) ?>
	</div>

	<?= $form->field($model, 'details')->textarea(['rows' => 6]) ?>


	<div class="form-group">
		<?= Html::submitButton('Zapisz', ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
