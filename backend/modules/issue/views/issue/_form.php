<?php

use common\modules\address\widgets\AddressFormWidget;
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

			<?= $form->field($model, 'type_id', ['options' => ['class' => 'col-md-3']])
				->widget(Select2::class, [
						'data' => IssueForm::getTypes(),
						'options' => [
							'placeholder' => 'Typ',
							'id' => 'issueTypeId',
						],
					]
				) ?>

			<?= $form->field($model, 'stage_id', ['options' => ['class' => 'col-md-3'],])
				->widget(DepDrop::class, [
					'type' => DepDrop::TYPE_SELECT2,
					'data' => $model->type_id !== null ? IssueForm::getStages($model->type_id) : [],
					'pluginOptions' => [
						'depends' => ['issueTypeId'],
						'placeholder' => 'Etap',
						'url' => Url::to(['//issue/type/stages-list']),
						'loading' => 'Wyszukiwanie...',
					],
				])
			?>

			<?= $form->field($model, 'stage_change_at', [
				'options' => [
					'class' => 'col-md-3',
				],
			])
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


			<?= $form->field($model, 'archives_nr', ['options' => ['id' => 'archives-field', 'class' => 'col-md-3 required' . (!$model->isArchived() ? ' hidden' : '')]])->textInput(); ?>

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

		<div class="row">

			<?= $form->field($model, 'date', ['options' => ['class' => 'col-md-6']])
				->widget(DateTimeWidget::class,
					[
						'phpDatetimeFormat' => 'yyyy-MM-dd',
					]) ?>

			<?= $form->field($model, 'accident_at', [
				'options' => [
					'id' => 'accident_at_field',
					'class' => 'col-md-6' . (!$model->isAccident() ? ' hidden' : ''),
				],
			])
				->widget(DateTimeWidget::class,
					[
						'phpDatetimeFormat' => 'yyyy-MM-dd',
					]) ?>
		</div>

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
				<?= AddressFormWidget::widget([
					'form' => $form,
					'model' => $model->getClientAddress(),
					'copyOptions' => [
						'data-selector' => 'data-victim-input',
						'inputs' => [
							'state' => Html::getInputName($model->getVictimAddress(), 'stateId'),
							'province' => Html::getInputName($model->getVictimAddress(), 'provinceId'),
							'subProvince' => Html::getInputName($model->getVictimAddress(), 'subProvinceId'),
							'city' => Html::getInputName($model->getVictimAddress(), 'cityId'),
							'cityCode' => Html::getInputName($model->getVictimAddress(), 'cityCode'),
							'street' => Html::getInputName($model->getVictimAddress(), 'street'),
						],
					],
				])
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


				<?= AddressFormWidget::widget([
					'form' => $form,
					'model' => $model->getVictimAddress(),
				])
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
$positiveDecisionStageId = IssueForm::STAGE_POSITIVE_DECISION_ID;
$stageInputId = Html::getInputId($model, 'stage_id');

$typeAccidentId = IssueForm::ACCIDENT_ID;
$js = <<<JS

let stageInput = document.getElementById('$stageInputId');
let typeInput = document.getElementById(('issueTypeId'));
let archivesInput = document.getElementById('archives-field');
let accidentAtInput = document.getElementById('accident_at_field');
let stateChangeAtInput = document.getElementById('issue-stage_change_at');

function isArchived(){
	return parseInt(stageInput.value) === $archivesStageId;
}

function isPositiveDecision(){
	console.log(parseInt(stageInput.value) === $positiveDecisionStageId);
	return parseInt(stageInput.value) === $positiveDecisionStageId;
}

function isAccident(){
	return parseInt(typeInput.value) === $typeAccidentId;
}


	stageInput.onchange = function(){
	let value = parseInt(this.value);
	if(value === $archivesStageId){
		archivesInput.classList.remove('hidden');
	}else{
		archivesInput.classList.add('hidden');
	}
	
	stateChangeAtInput.value = '';
};

typeInput.onchange= function(){
	if(parseInt(this.value) === $typeAccidentId){
		accidentAtInput.classList.remove('hidden');
	}else{
		accidentAtInput.classList.add('hidden');
	}
};

document.querySelector('.client-fieldset .copy-btn').addEventListener('click', function(evt) {
	let attributeName = evt.currentTarget.getAttribute('data-copy');
    let parentFields = document.querySelectorAll('['+attributeName +']');
    for(let i =0, field; !!(field = parentFields[i]); i++){
    	let toCopyField = document.querySelector("[name='"+field.getAttribute(attributeName) +"']");
		if(toCopyField.value !== field.value){
			toCopyField.value = field.value;
			let toCopy = $(toCopyField);
			toCopy.trigger('change');
            if(toCopyField instanceof HTMLSelectElement){
       	        toCopy.trigger('select2:select');
            }
		}
    
     
    }
});



JS;

$this->registerJs($js);
