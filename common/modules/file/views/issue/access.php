<?php

use backend\helpers\Breadcrumbs;
use common\helpers\Html;
use common\modules\file\models\IssueFileAccess;
use common\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model IssueFileAccess */

$this->title = Yii::t('file', 'Access to {name}', [
		'issue' => $model->getIssueFile()->issue->getIssueName(),
		'name' => $model->getIssueFile()->file->name,
	]
);
$this->params['breadcrumbs'] = Breadcrumbs::issue($model->getIssueFile()->issue);
$this->params['breadcrumbs'][] = [
	'url' => ['view', 'issue_id' => $model->getIssueFile()->issue_id, 'file_id' => $model->getIssueFile()->file_id],
	'label' => $model->getIssueFile()->file->name,
];
?>

<div class="issue-file-access">
	<div class="row">
		<div class="col-md-6">
			<div class="issue-file-access-form">
				<?php $form = ActiveForm::begin(); ?>

				<?= $form->field($model, 'user_id')->widget(Select2::class, [
					'data' => $model->getUsersNames(),
				]) ?>


				<div class="form-group">
					<?= Html::submitButton(Yii::t('file', 'Add Access'), ['class' => 'btn btn-success']) ?>
				</div>

				<?php ActiveForm::end(); ?>
			</div>
		</div>

		<div class="col-md-6">
			<?= $this->render('_access_grid', [
				'model' => $model->getIssueFile(),
			]) ?>
		</div>
	</div>

</div>
