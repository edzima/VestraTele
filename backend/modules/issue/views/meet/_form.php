<?php

use backend\modules\address\widgets\AddressWidget;
use common\models\issue\IssueMeet;
use common\models\User;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\issue\IssueMeet */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="issue-meet-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'campaign_id')->dropDownList(IssueMeet::getCampaignNames()) ?>

	<?= $form->field($model, 'type_id')->dropDownList(IssueMeet::getTypesNames()) ?>

	<?= $form->field($model, 'status')->dropDownList(IssueMeet::getStatusNames()) ?>

	<fieldset>
		<legend>Klient</legend>
		<div class="row">
			<?= $form->field($model, 'client_name', [
				'options' => [
					'class' => 'col-md-4',
				],
			])->textInput(['maxlength' => true]) ?>

			<?= $form->field($model, 'client_surname', [
				'options' => [
					'class' => 'col-md-4',
				],
			])->textInput(['maxlength' => true]) ?>

			<?= $form->field($model, 'phone', [
				'options' => [
					'class' => 'col-md-4',
				],
			])->textInput(['maxlength' => true]) ?>
		</div>
		<?= AddressWidget::widget([
			'form' => $form,
			'model' => $model,
			'state' => 'stateId',
			'province' => 'provinceId',
			'subProvince' => 'sub_province_id',
			'city' => 'city_id',
			'street' => 'street',
		]) ?>
	</fieldset>


	<div class="row">
		<?= $form->field($model, 'tele_id', ['options' => ['class' => 'col-md-6']])
			->widget(Select2::class, [
					'data' => User::getSelectList([User::ROLE_TELEMARKETER]),
					'options' => [
						'placeholder' => 'Tele',
					],
					'pluginOptions' => [
						'allowClear' => true,
					],
				]
			) ?>

		<?= $form->field($model, 'agent_id', ['options' => ['class' => 'col-md-6']])
			->widget(Select2::class, [
					'data' => User::getSelectList([User::ROLE_AGENT]),
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
