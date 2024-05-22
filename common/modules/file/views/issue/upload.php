<?php

use common\helpers\Breadcrumbs;
use common\helpers\Html;
use common\models\issue\IssueInterface;
use common\modules\file\models\UploadForm;
use common\modules\file\widgets\FileInput;
use common\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $issue IssueInterface */
/* @var $model UploadForm */

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


		<?php ActiveForm::end(); ?>

	</div>

</div>
