<?php

use common\helpers\Html;
use common\models\issue\form\IssueStageChangeForm;
use common\models\issue\IssueStage;
use common\modules\issue\widgets\IssueMessagesFormWidget;
use common\widgets\ActiveForm;
use common\widgets\AutoCompleteTextarea;
use common\widgets\DateTimeWidget;
use kartik\select2\Select2;
use yii\helpers\Json;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model IssueStageChangeForm */
/* @var $noteDescriptionUrl string */

$stageInputId = Html::getInputId($model, 'stage_id');
$archivesIds = Json::encode(IssueStage::ARCHIVES_IDS);

$customerSmsMessages = Json::encode($model->getMessagesModel()->getCustomerSmsMessages());
$customerSmsInputFieldClass = 'field-' . Html::getInputId($model->getMessagesModel(), 'sendSmsToCustomer');
$workersSmsMessages = Json::encode(null);
$workersSmsInputFieldClass = 'field-' . Html::getInputId($model->getMessagesModel(), 'sendSmsToWorkers');

$js = <<<JS

const customerSmsField = document.getElementsByClassName('$customerSmsInputFieldClass')[0];
const customerSmsMessages = $customerSmsMessages;

const workersSmsField = document.getElementsByClassName('$workersSmsInputFieldClass')[0];
const workersSmsMessages = $workersSmsMessages;


const stageInput = document.getElementById('$stageInputId');
const archivesField = document.getElementById('archives-field');
const archivesIds = $archivesIds;



function updateMessagesText(stage_id){
	updateCustomerSmsBlockText(stage_id);
	updateWorkersSmsBlockText(stage_id);
}

function updateCustomerSmsBlockText(stage_id){
	updateMessageBlockText(stage_id, customerSmsMessages, customerSmsField);
}

function updateWorkersSmsBlockText(stage_id){
	updateMessageBlockText(stage_id, workersSmsMessages, workersSmsField);
}


function updateMessageBlockText(stage_id, messages, field){
		if(messages){
		let defaultTemplate = null;
		let currentStageTemplate = null;
		messages.forEach((message) =>{
			if(message.stage_id === null){
				defaultTemplate = message.message;
			}
			if(message.stage_id === stage_id){
				currentStageTemplate = message.message;
			}
		});
		field.getElementsByClassName('help-block-templates');
		let messageEleement = field.getElementsByClassName('help-block-templates')[0];
		if(messageEleement === undefined){
			messageEleement = document.createElement('p');
			messageEleement.classList.add('help-block');
			messageEleement.classList.add('help-block-templates');
			field.append(messageEleement);
		}
		if(defaultTemplate === null && currentStageTemplate === null){
			messageEleement.textContent = '';
		}else{
			if(currentStageTemplate){
				messageEleement.textContent =currentStageTemplate;
			}else{
				messageEleement.textContent = defaultTemplate;
			}
		}
		
	}
}

stageInput.onchange = function(){
	let value = parseInt(this.value);
	updateMessagesText(value);
	if(archivesIds.includes(value)){
		archivesField.classList.remove('hidden');
	}else{
		archivesField.classList.add('hidden');
	}
	
};

updateMessagesText(parseInt(stageInput.value));


JS;

$this->registerJs($js, View::POS_LOAD);

?>


<div class="issue-stage-change">

	<div class="row">
		<div class="col-md-6">
			<?= DetailView::widget([
				'model' => $model->getIssue(),
				'attributes' => [
					'type',
					'stage',
				],
			]) ?>

		</div>
	</div>
	<div class="row">
		<div class="issue-stage-form col-md-6">

			<?php $form = ActiveForm::begin(
				['id' => 'issue-stage-form']
			); ?>
			<div class="row">
				<?= !empty($model->getLinkedIssuesNames())
					? $form->field($model, 'linkedIssues', [
						'options' => [
							'class' => 'col-md-12',
						],
					])
						->widget(Select2::class, [
							'data' => $model->getLinkedIssuesNames(),
							'options' => [
								'multiple' => true,
							],
						])
						->hint(Yii::t('issue', 'Change Stage also in Linked Issues.'))
					: ''
				?>



				<?= $form->field($model, 'stage_id', [
					'options' => [
						'class' => 'col-md-8',
					],
				])
					->widget(Select2::class, [
						'data' => $model->getStagesData(),
					])
				?>




				<?= $form->field($model, 'date_at', [
					'options' => [
						'class' => 'col-md-4',
					],
				])
					->widget(DateTimeWidget::class, [
						'phpDatetimeFormat' => 'yyyy-MM-dd HH:mm:ss',
					])
				?>


				<?= $form->field($model, 'archives_nr', [
					'options' => [
						'id' => 'archives-field',
						'class' => 'col-md-4' . (!empty($model->archives_nr)
							|| in_array($model->stage_id, IssueStage::ARCHIVES_IDS)
							|| $model->getIssue()->getIssueModel()->isArchived()
								? ''
								: ' hidden'
							),
					],
				])->textInput()

				?>

			</div>

			<?= !empty($noteDescriptionUrl)
				? $form->field($model, 'description', [
					'options' => [
						'class' => 'select-text-area-field',
					],
				])->widget(AutoCompleteTextarea::class, [
					'clientOptions' => [
						'source' => $noteDescriptionUrl,
						'autoFocus' => true,
						'delay' => 500,
						'minLength' => 5,
					],
					'options' => [
						'rows' => 5,
						'class' => 'form-control',
					],
				])
				: ''
			?>



			<?= IssueMessagesFormWidget::widget([
				'form' => $form,
				'model' => $model->getMessagesModel(),
			]) ?>

			<?= !empty($model->getLinkedIssuesNames())
				? $form->field($model, 'linkedIssuesMessages')->checkbox()
				: ''
			?>


			<div class="form-group">
				<?= Html::submitButton(Yii::t('common', 'Save'), ['class' => 'btn btn-success']) ?>
			</div>

			<?php ActiveForm::end(); ?>
		</div>


	</div>


</div>



