<?php

use common\helpers\Breadcrumbs;
use common\helpers\Html;
use common\models\issue\IssueInterface;
use common\models\message\IssueFilesUploadMessagesForm;
use common\modules\file\models\UploadForm;
use common\modules\file\widgets\FileInput;
use common\modules\issue\widgets\IssueMessagesFormWidget;
use common\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $issue IssueInterface */
/* @var $model UploadForm */
/* @var $messages IssueFilesUploadMessagesForm */

$this->title = Yii::t('file', 'Upload -{type} to Issue: {issue}', [
		'issue' => $issue->getIssueName(),
		'type' => $model->getType()->name,
	]
);
$this->params['breadcrumbs'] = Breadcrumbs::issue($issue);
$this->params['breadcrumbs'][] = $model->getType()->name;

?>
<div class="issue-file-upload">

	<h1><?= Html::encode($this->title) ?></h1>

	<div class="issue-attachments-form">

		<?php $form = ActiveForm::begin(
			['id' => 'issue-attachments-form']
		); ?>

		<?= $form->field($model, 'file[]')->widget(FileInput::class, [
			'options' => [
				'multiple' => true,
			],
			'previewFiles' => $issue->getFilesByType($model->getType()->id),
			'previewRoute' => ['/file/issue/download', 'issue_id' => $issue->getIssueId()],
			'deleteRoute' => ['/file/issue/delete', 'issue_id' => $issue->getIssueId()],
		]) ?>

		<?= IssueMessagesFormWidget::widget([
			'form' => $form,
			'model' => $messages,
			'checkboxesAttributes' => [
				'sendEmailToWorkers',
			],
		]) ?>

		<?php ActiveForm::end(); ?>

	</div>

</div>
