<?php

use backend\modules\issue\models\IssueForm;
use common\models\message\IssueCreateMessagesForm;
use common\modules\issue\widgets\IssueMessagesFormWidget;
use common\widgets\DateTimeWidget;
use common\widgets\DateWidget;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use vova07\imperavi\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model IssueForm */
/* @var $form yii\widgets\ActiveForm */
/* @var $messagesModel IssueCreateMessagesForm|null */

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
					'select2Options' => [
						'disabled' => !$model->getModel()->isNewRecord,
					],
					'pluginEvents' => [
						"depdrop:afterChange" => "function(event, id, value) { event.currentTarget.disabled = false; }",
					],
					'pluginOptions' => [
						'disabled' => true,
						'depends' => [Html::getInputId($model, 'type_id')],
						'placeholder' => $model->getAttributeLabel('stage_id'),
						'url' => Url::to(['//issue/type/stages-list', 'stageId' => $model->stage_id]),
						'loading' => Yii::t('common', 'Loading...'),
					],
				])
			?>

			<?= $form->field($model, 'signing_at', ['options' => ['class' => 'col-md-2']])
				->widget(DateWidget::class) ?>


			<?= !empty($model->stage_change_at)
				? $form->field($model, 'stage_change_at', [
					'options' => [
						'class' => 'col-md-3 col-lg-2',
					],
				])
					->widget(DateTimeWidget::class, ['phpDatetimeFormat' => 'yyyy-MM-dd'])
				: '' ?>


			<?= $form->field($model, 'stage_deadline_at', [
				'options' => [
					'class' => 'col-md-3 col-lg-2',
				],
			])
				->widget(DateTimeWidget::class, ['phpDatetimeFormat' => 'yyyy-MM-dd'])
			?>


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



			<?= $form->field($model, 'entity_agreement_at', ['options' => ['class' => 'col-md-2']])
				->widget(DateWidget::class) ?>



			<?= $form->field($model, 'entity_agreement_details', ['options' => ['class' => 'col-md-2']])
				->textarea([
					'row' => 2,
				])
			?>


			<?= $form->field($model, 'signature_act', ['options' => ['class' => 'col-md-2']])
				->textarea([
					'row' => 2,
				])
			?>


			<?= $form->field($model, 'type_additional_date_at', [
				'options' => [
					'id' => 'field-' . Html::getInputId($model, 'type_additional_date_at'),
					'class' => 'col-md-2' . (
						!isset($model::getTypesWithAdditionalDateNames()[(int) $model->type_id]) ?
							' hidden' : ''),
				],
			])
				->widget(DateWidget::class) ?>

		</div>

		<div class="row">


		</div>

		<div class="row">
			<?= $form->field($model, 'tagsIds', ['options' => ['class' => 'col-md-5']])
				->widget(Select2::class, [
					'data' => IssueForm::getTagsNames($model->getModel()->isNewRecord),
					'options' => [
						'multiple' => true,
					],
					'pluginOptions' => [
						'tags' => true,
					],
				]) ?>

		</div>

		<div class="row">


			<?= $form->field($model, 'details', ['options' => ['class' => 'col-md-5']])
				->widget(Widget::class) ?>

			<?= !empty($model->getLinkedIssuesNames())
				? $form->field($model, 'linkedIssuesIds', [
					'options' => [
						'class' => 'col-md-6',
					],
				])
					->widget(Select2::class, [
						'data' => $model->getLinkedIssuesNames(),
						'options' => [
							'multiple' => true,
						],
					])
					->hint(Yii::t('issue', 'Update linked Issues.'))
				: ''
			?>

			<?= !empty($model->getLinkedIssuesNames())
				? $form->field($model, 'linkedIssuesAttributes', [
					'options' => [
						'class' => 'col-md-6 hidden',
						'id' => 'field-' . Html::getInputId($model, 'linkedIssuesAttributes'),
					],
				])
					->widget(Select2::class, [
						'data' => $model->getLinkedAttributesNames(),
						'options' => [
							'multiple' => true,
						],
					])
				: ''
			?>

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

		<?php if ($messagesModel): ?>
			<div class="row">
				<div class="col-md-6 col-lg-3">
					<?= IssueMessagesFormWidget::widget([
						'form' => $form,
						'model' => $messagesModel,
					]) ?>
				</div>
			</div>
		<?php endif; ?>


		<div class="form-group">
			<?= Html::submitButton('Zapisz', ['class' => 'btn btn-success']) ?>
		</div>


		<?php ActiveForm::end(); ?>

	</div>

<?php

$archivesStageId = IssueForm::STAGE_ARCHIVED_ID;
$stageInputId = Html::getInputId($model, 'stage_id');
$typeInputId = Html::getInputId($model, 'type_id');
$typeAdditionalInputId = 'field-' . Html::getInputId($model, 'type_additional_date_at');

$typesWithAdditionalDateAtNames = Json::encode($model::getTypesWithAdditionalDateNames());

$stageChangeInputId = Html::getInputId($model, 'stage_change_at');

$linkedIdsField = Html::getInputId($model, 'linkedIssuesIds');
$linkedAttributesField = 'field-' . Html::getInputId($model, 'linkedIssuesAttributes');

$js = <<<JS

const stageInput = document.getElementById('$stageInputId');
const typeInput = document.getElementById('$typeInputId');
const typeAdditionalDateAtField = document.getElementById('$typeAdditionalInputId');
const labelForTypeAdditionalDateAtField = typeAdditionalDateAtField.getElementsByTagName('label')[0];
const typesAdditionalDateAtNames = $typesWithAdditionalDateAtNames;
const archivesField = document.getElementById('archives-field');


const linkedIssuesIdsInput = document.getElementById('$linkedIdsField');
const linkedAttributesField = document.getElementById('$linkedAttributesField');

function archivesFieldVisibility(){
	if(isArchived()){
		archivesField.classList.remove('hidden');
	}else{
		archivesField.classList.add('hidden');
	}
}


function isArchived(){
	return parseInt(stageInput.value) === $archivesStageId;
}


stageInput.onchange = function(){
	archivesFieldVisibility();
};

function linkedAttributesFieldVisibility(){
	if(linkedIssuesIdsInput.value.length){
		linkedAttributesField.classList.remove('hidden');
	}else{
		linkedAttributesField.classList.add('hidden');
	}
}

if(linkedIssuesIdsInput){
	linkedIssuesIdsInput.onchange = function(){
		linkedAttributesFieldVisibility();
	};
	linkedAttributesFieldVisibility();
}

function typesAdditionalDateVisibility(){
	if(typesAdditionalDateAtNames.hasOwnProperty(parseInt(this.value))){
		labelForTypeAdditionalDateAtField.textContent = typesAdditionalDateAtNames[parseInt(this.value)];
		typeAdditionalDateAtField.classList.remove('hidden');
	}else{
		typeAdditionalDateAtField.classList.add('hidden');
	}
}

typeInput.onchange= function(){
	typesAdditionalDateVisibility();
};

archivesFieldVisibility();
typesAdditionalDateVisibility();



JS;

$this->registerJs($js);
