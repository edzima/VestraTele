<?php

use backend\modules\issue\models\IssueForm;
use common\widgets\DateTimeWidget;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model IssueForm */
/* @var $form yii\widgets\ActiveForm */

?>
	<div class="issue-form">

		<?php $form = ActiveForm::begin(
			['id' => 'issue-form']
		); ?>

		<div class="row">

			<?= $form->field($model, 'type_id', [
				'options' => [
					'class' => 'col-md-3',
				],
			])
				->widget(Select2::class, [
						'data' => IssueForm::getTypesNames(),
						'options' => [
							'placeholder' => $model->getAttributeLabel('type_id'),
						],
					]
				) ?>

			<?= $form->field($model, 'archives_nr', [
				'options' => [
					'id' => 'archives-field',
					'class' => 'col-md-3 col-lg-1 required' . (!$model->getModel()->isArchived() ? ' hidden' : ''),
				],
			])->textInput(); ?>


			<?= $form->field($model, 'stage_id', [
				'options' => [
					'class' => 'col-md-3 col-lg-2',
				],
			])
				->widget(DepDrop::class, [
					'type' => DepDrop::TYPE_SELECT2,
					'data' => $model->getStagesData(),
					'pluginOptions' => [
						'depends' => [Html::getInputId($model, 'type_id')],
						'placeholder' => $model->getAttributeLabel('stage_id'),
						'url' => Url::to(['//issue/type/stages-list']),
						'loading' => Yii::t('common', 'Loading...'),
					],
				])
			?>

			<?= !empty($model->stage_change_at)
				? $form->field($model, 'stage_change_at', [
					'options' => [
						'class' => 'col-md-3 col-lg-2',
					],
				])
					->widget(DateTimeWidget::class, ['phpDatetimeFormat' => 'yyyy-MM-dd'])
				: '' ?>


		</div>


		<div class="row">


			<?= $form->field($model, 'entity_responsible_id', ['options' => ['class' => 'col-md-3']])
				->widget(Select2::class, [
						'data' => IssueForm::getEntityResponsibles(),
						'options' => [
							'placeholder' => 'Podmiot odpowiedzialny',
						],
					]
				) ?>

			<?= $form->field($model, 'signature_act', ['options' => ['class' => 'col-md-2']])
				->textInput() ?>

			<?= $form->field($model, 'date', ['options' => ['class' => 'col-md-2']])
				->widget(DateTimeWidget::class,
					[
						'phpDatetimeFormat' => 'yyyy-MM-dd',
					]) ?>

			<?= $form->field($model, 'accident_at', [
				'options' => [
					'id' => 'accident_at_field',
					'class' => 'col-md-2' . (!$model->getModel()->isAccident() ? ' hidden' : ''),
				],
			])
				->widget(DateTimeWidget::class,
					[
						'phpDatetimeFormat' => 'yyyy-MM-dd',
					]) ?>

		</div>

		<div class="row">
			<?= $form->field($model, 'details', ['options' => ['class' => 'col-md-5']])
				->textarea(['rows' => 5, 'maxlength' => true]) ?>

		</div>

		<fieldset>
			<legend>Role</legend>
			<div class="row">

				<?= $form->field($model, 'agent_id', ['options' => ['class' => 'col-md-3']])
					->widget(Select2::class, [
							'data' => IssueForm::getAgents(),
							'options' => [
								'placeholder' => $model->getAttributeLabel('agent_id'),
							],
						]
					) ?>


				<?= $form->field($model, 'lawyer_id', ['options' => ['class' => 'col-md-3']])
					->widget(Select2::class, [
							'data' => IssueForm::getLawyers(),
							'options' => [
								'placeholder' => $model->getAttributeLabel('lawyer_id'),
							],
						]
					) ?>

				<?= $form->field($model, 'tele_id', ['options' => ['class' => 'col-md-3']])
					->widget(Select2::class, [
							'data' => IssueForm::getTele(),
							'options' => [
								'placeholder' => $model->getAttributeLabel('tele_id'),
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
$typeInputId = Html::getInputId($model, 'type_id');
$stageChangeInputId = Html::getInputId($model, 'stage_change_at');

$typeAccidentId = IssueForm::TYPE_ACCIDENT_ID;
$js = <<<JS

let stageInput = document.getElementById('$stageInputId');
let typeInput = document.getElementById('$typeInputId');
let archivesField = document.getElementById('archives-field');
let accidentAtField = document.getElementById('accident_at_field');
let stateChangeAtInput = document.getElementById('$stageChangeInputId');

function isArchived(){
	return parseInt(stageInput.value) === $archivesStageId;
}

function isPositiveDecision(){
	return parseInt(stageInput.value) === $positiveDecisionStageId;
}

function isAccident(){
	return parseInt(typeInput.value) === $typeAccidentId;
}


	stageInput.onchange = function(){
	let value = parseInt(this.value);
	if(value === $archivesStageId){
		archivesField.classList.remove('hidden');
	}else{
		archivesField.classList.add('hidden');
	}
	if(stateChangeAtInput){
			stateChangeAtInput.value = '';
	}
	
};

typeInput.onchange= function(){
	if(parseInt(this.value) === $typeAccidentId){
		accidentAtField.classList.remove('hidden');
	}else{
		accidentAtField.classList.add('hidden');
	}
};



JS;

$this->registerJs($js);
