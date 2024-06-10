<?php

use common\helpers\Breadcrumbs;
use common\helpers\Html;
use common\helpers\Url;
use common\models\issue\IssueInterface;
use common\models\message\IssueFilesUploadMessagesForm;
use common\modules\file\helpers\FilePreviewHelper;
use common\modules\file\widgets\FileInput;
use common\modules\issue\widgets\IssueMessagesFormWidget;
use common\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $issue IssueInterface */
/* @var $model IssueFilesUploadMessagesForm */

$this->title = Yii::t('file', 'Upload -{type} to Issue: {issue}', [
		'issue' => $issue->getIssueName(),
		'type' => $model->getFileType()->name,
	]
);
$this->params['breadcrumbs'] = Breadcrumbs::issue($issue);
$this->params['breadcrumbs'][] = $model->getFileType()->name;
?>
<div class="issue-file-upload">

	<h1><?= Html::encode($this->title) ?></h1>

	<div class="issue-attachments-form">

		<?php $form = ActiveForm::begin(
			['id' => 'issue-attachments-form']
		); ?>

		<?=
		FileInput::widget([
			'name' => 'file',
			'options' => [
				'multiple' => true,
				'accept' => $model->getFileType()->getAcceptExtensions(),
			],
			'filePreviewHelper' => FilePreviewHelper::createForIssue($issue->getIssueId()),
			'previewFiles' => Yii::$app->fileAuth
				->filterUserFiles(
					Yii::$app->user->getId(),
					$issue->getFilesByType($model->getFileType()->id),
					$issue->getIssueModel()->getUserRoles(Yii::$app->user->getId())
				),
			'pluginOptions' => [
				'uploadUrl' => Url::to(['/file/issue/single-upload', 'issue_id' => $issue->getIssueId(), 'file_type_id' => $model->getFileType()->id]),
			],
		]) ?>


		<?= IssueMessagesFormWidget::widget([
			'form' => $form,
			'model' => $model,
			'checkboxesAttributes' => ['sendEmailToWorkers',],
		]) ?>

		<?= Html::submitButton(Yii::t('common', 'Save'), [
			'class' => 'btn btn-primary',
		]) ?>

		<?php ActiveForm::end(); ?>

	</div>

</div>
