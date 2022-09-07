<?php

use common\helpers\Html;
use common\models\issue\IssueNoteForm;
use common\modules\issue\widgets\IssueMessagesFormWidget;
use common\widgets\ActiveForm;
use common\widgets\AutoCompleteTextarea;
use common\widgets\DateTimeWidget;
use kartik\select2\Select2;
use yii\web\View;

/* @var $this View */
/* @var $model IssueNoteForm */
/* @var array $options */
/* @var $titleUrl string */
/* @var $descriptionUrl string */

?>

<div class="issue-note-form">

	<?php $form = ActiveForm::begin($options); ?>

	<div class="row">
		<?= $form->field($model, 'publish_at', [
			'options' => ['class' => 'col-md-2'],
		])->widget(DateTimeWidget::class, [
			'phpDatetimeFormat' => 'yyyy-MM-dd HH:mm:ss',
		]) ?>

		<?= $model->getScenario() === IssueNoteForm::SCENARIO_STAGE_CHANGE ? $form->field($model, 'stageChangeAtMerge', [
			'options' => ['class' => 'col-md-2'],
		])->checkbox()
			: '' ?>

		<?= $form->field($model, 'is_pinned', [
			'options' => ['class' => 'col-md-1'],
		])->checkbox() ?>

		<?= $model->isAttributeSafe('is_template')
			? $form->field($model, 'is_template', [
				'options' => ['class' => 'col-md-1'],
			])->checkbox()
			: ''
		?>
	</div>


	<?= $model->getModel()->isNewRecord && !empty($model->getLinkedIssuesNames())
		? $form->field($model, 'linkedIssues')
			->widget(Select2::class, [
				'data' => $model->getLinkedIssuesNames(),
				'options' => [
					'multiple' => true,
				],
			])
			->hint(Yii::t('issue', 'Create Note also in Linked Issues.'))
		: ''
	?>

	<?= $model->getScenario() !== IssueNoteForm::SCENARIO_STAGE_CHANGE
		? $form->field($model, 'title')->widget(AutoCompleteTextarea::class, [
			'clientOptions' => [
				'source' => $titleUrl,
				'autoFocus' => true,
				'delay' => 500,
				'minLength' => 3,
			],
			'options' => [
				'rows' => 2,
				'class' => 'form-control',
			],
		])
		: $form->field($model, 'title')->textInput(['disabled' => 1])
	?>

	<?= $form->field($model, 'description', [
		'options' => [
			'class' => 'select-text-area-field',
		],
	])->widget(AutoCompleteTextarea::class, [
		'clientOptions' => [
			'source' => $descriptionUrl,
			'autoFocus' => true,
			'delay' => 500,
			'minLength' => 3,
		],
		'options' => [
			'rows' => 5,
			'class' => 'form-control',
		],
	])
	?>


	<?= $model->messagesForm !== null
		? IssueMessagesFormWidget::widget([
			'model' => $model->messagesForm,
			'form' => $form,
			'checkboxesAttributes' => [
				'sendSmsToCustomer',
				'sendEmailToWorkers',
			],
		])
		: ''
	?>

	<?= $model->getModel()->isNewRecord && $model->messagesForm !== null && !empty($model->getLinkedIssuesNames())
		? $form->field($model, 'linkedIssuesMessages')->checkbox()
		: ''
	?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('common', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

