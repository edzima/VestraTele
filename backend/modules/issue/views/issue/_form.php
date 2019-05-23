<?php

use backend\modules\address\widgets\AddressWidget;
use common\models\issue\Issue;
use common\models\issue\Provision;
use common\widgets\DateTimeWidget;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;
use backend\modules\issue\models\IssueForm;

/* @var $this View */
/* @var $model Issue */
/* @var $form yii\widgets\ActiveForm */

?>

	<div class="issue-form">

		<?php $form = ActiveForm::begin(); ?>

		<div class="row">

			<?= $form->field($model, 'type_id', ['options' => ['class' => 'col-md-4']])
				->widget(Select2::class, [
						'data' => IssueForm::getTypes(),
						'options' => [
							'placeholder' => 'Typ',
							'id' => 'issueTypeId',
						],
					]
				) ?>

			<?= $form->field($model, 'stage_id', ['options' => ['class' => 'col-md-4'],])
				->widget(DepDrop::class, [
					'type' => DepDrop::TYPE_SELECT2,
					'data' => $model->type_id !== null ? IssueForm::getStages($model->type_id) : [],
					'pluginOptions' => [
						'depends' => ['issueTypeId'],
						'placeholder' => 'Etap',
						'url' => Url::to(['//issue/type/stages-list']),
						'loading' => 'Wyszukiwanie...',
					],
				]);
			?>


			<?= $form->field($model, 'archives_nr', ['options' => ['id' => 'archives-field', 'class' => 'col-md-2 required' . (!$model->isArchived() ? ' hidden' : '')]])->textInput(); ?>

		</div>

		<div class="row">
			<?= $form->field($model, 'agent_id', ['options' => ['class' => 'col-md-6']])
				->widget(Select2::class, [
						'data' => IssueForm::getAgents(),
						'options' => [
							'placeholder' => 'Agent',
						],
					]
				) ?>

			<?= $form->field($model, 'entity_responsible_id', ['options' => ['class' => 'col-md-6']])
				->widget(Select2::class, [
						'data' => IssueForm::getEntityResponsibles(),
						'options' => [
							'placeholder' => 'Podmiot odpowiedzialny',
						],
					]
				) ?>

		</div>


		<?= $form->field($model, 'date')
			->widget(DateTimeWidget::class,
				[
					'phpDatetimeFormat' => 'yyyy-MM-dd',
					'clientOptions' => [

						'allowInputToggle' => true,
						'sideBySide' => true,
						'widgetPositioning' => [
							'horizontal' => 'auto',
							'vertical' => 'auto',
						],
					],
				]) ?>

		<?= $form->field($model, 'details')->textarea(['rows' => 10, 'maxlength' => true]) ?>

		<div class="row">
			<fieldset class="col-md-6 client-fieldset">
				<style>
					.client-fieldset {
						position: relative;
					}

					.client-fieldset .copy-btn {
						position: absolute;
						right: 15px;
						top: -48px;
					}
				</style>
				<legend>Dane klienta</legend>
				<?= Html::button('Copy', ['class' => 'copy-btn', 'data-copy' => 'data-victim-input']); ?>
				<div class="row">
					<?= $form->field(
						$model,
						'client_first_name', [
						'options' => [
							'class' => 'col-md-6',
						],
					])->textInput(
						[
							'maxlength' => true,
							'data-victim-input' => Html::getInputName($model, 'victim_first_name'),
						]) ?>

					<?= $form->field(
						$model,
						'client_surname', [
						'options' => [
							'class' => 'col-md-6',
						],
					])->textInput([
						'maxlength' => true,
						'data-victim-input' => Html::getInputName($model, 'victim_surname'),
					]) ?>
				</div>
				<div class="row">
					<?= $form->field(
						$model,
						'client_phone_1', [
						'options' => [
							'class' => 'col-md-6',
						],
					])->textInput([
						'maxlength' => true,
						'data-victim-input' => Html::getInputName($model, 'victim_phone'),
					]) ?>

					<?= $form->field(
						$model,
						'client_phone_2', [
						'options' => [
							'class' => 'col-md-6',
						],
					])->textInput(['maxlength' => true]) ?>
				</div>
				<div class="row">
					<?= $form->field(
						$model,
						'client_email', [
						'options' => [
							'class' => 'col-md-12',
						],
					])->textInput([
						'maxlength' => true,
						'data-victim-input' => Html::getInputName($model, 'victim_email'),
					]) ?>
				</div>
				<?= AddressWidget::widget([
					'form' => $form,
					'model' => $model,
					'state' => 'clientStateId',
					'province' => 'clientProvinceId',
					'subProvince' => 'client_subprovince_id',
					'city' => 'client_city_id',
					'cityCode' => 'client_city_code',
					'street' => 'client_street',
					'copyOptions' => [
						'data-selector' => 'data-victim-input',
						'inputs' => [
							'state' => Html::getInputName($model, 'victim_state_id'),
							'province' => Html::getInputName($model, 'victim_province_id'),
							'subProvince' => Html::getInputName($model, 'victim_subprovince_id'),
							'city' => Html::getInputName($model, 'victim_city_id'),
							'cityCode' => Html::getInputName($model, 'victim_city_code'),
							'street' => Html::getInputName($model, 'victim_street'),
						],
					],
				]);
				?>

			</fieldset>

			<fieldset class="col-md-6">
				<legend>Dane poszkodowanego</legend>

				<div class="row">
					<?= $form->field($model, 'victim_first_name', ['options' => ['class' => 'col-md-6']])->textInput(['maxlength' => true]) ?>

					<?= $form->field($model, 'victim_surname', ['options' => ['class' => 'col-md-6']])->textInput(['maxlength' => true]) ?>
				</div>
				<div class="row">
					<?= $form->field($model, 'victim_phone', ['options' => ['class' => 'col-md-12']])->textInput(['maxlength' => true]) ?>
				</div>
				<div class="row">
					<?= $form->field($model, 'victim_email', ['options' => ['class' => 'col-md-12']])->textInput(['maxlength' => true]) ?>
				</div>


				<?= AddressWidget::widget([
					'form' => $form,
					'model' => $model,
					'state' => 'victim_state_id',
					'province' => 'victim_province_id',
					'subProvince' => 'victim_subprovince_id',
					'city' => 'victim_city_id',
					'cityCode' => 'victim_city_code',
					'street' => 'victim_street',
				]);
				?>
			</fieldset>
		</div>


		<fieldset>
			<legend>Prowizja</legend>
			<div class="row">

				<?= $form->field($model, 'provision_base', [
					'options' => ['class' => 'col-md-5 form-group'],
				])->textInput(['maxlength' => true]) ?>

				<?= $form->field($model, 'provision_value', [
					'options' => ['class' => 'col-md-5 form-group'],
				])->textInput(['maxlength' => true]) ?>


				<?= $form->field($model, 'provision_type', [
					'options' => ['class' => 'col-md-2 form-group'],
				])->dropDownList(Provision::getTypesNames()) ?>

			</div>
		</fieldset>

		<fieldset>
			<legend>Role</legend>
			<div class="row">
				<?= $form->field($model, 'lawyer_id', ['options' => ['class' => 'col-md-6']])
					->widget(Select2::class, [
							'data' => IssueForm::getLawyers(),
							'options' => [
								'placeholder' => 'Prawnik',
							],
							'pluginOptions' => [
								'allowClear' => true,
							],
						]
					) ?>

				<?= $form->field($model, 'tele_id', ['options' => ['class' => 'col-md-6']])
					->widget(Select2::class, [
							'data' => IssueForm::getTele(),
							'options' => [
								'placeholder' => 'Telemarketer',
							],
							'pluginOptions' => [
								'allowClear' => true,
							],
						]
					) ?>
			</div>
		</fieldset>

		<div class="form-group">
			<?= Html::submitButton('Zapisz', ['class' => 'btn btn-success']) ?>
		</div>


		<?php ActiveForm::end(); ?>

	</div>

<?php
$archivesStageId = IssueForm::STAGE_ARCHIVED_ID;
$stageInputId = Html::getInputId($model, 'stage_id');
$js = <<<JS

let stageInput = document.getElementById('$stageInputId');
let archivesInput = document.getElementById('archives-field');

function isArchived(){
	return parseInt(stageInput.value) === $archivesStageId;
}

stageInput.onchange = function(){
	if(parseInt(this.value) === $archivesStageId){
		archivesInput.classList.remove('hidden');
	}else{
		archivesInput.classList.add('hidden');
	}
}

document.querySelector('.client-fieldset .copy-btn').addEventListener('click', function(evt) {
	let attributeName = evt.currentTarget.getAttribute('data-copy');
    let parentFields = document.querySelectorAll('['+attributeName +']');
    for(let i =0, field; !!(field = parentFields[i]); i++){
    	let toCopyField = document.querySelector("[name='"+field.getAttribute(attributeName) +"']");
    	toCopyField.value = field.value;
    	var toCopy = $(toCopyField);
        toCopy.trigger('change');
        if(toCopyField instanceof HTMLSelectElement){
        	toCopy.trigger('select2:select');
        }
    }
});
JS;

$this->registerJs($js);