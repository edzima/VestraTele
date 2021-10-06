<?php

use backend\helpers\Breadcrumbs;
use common\helpers\Html;
use common\models\issue\IssueSmsForm;
use common\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\View;

/* @var $this View */
/* @var $model IssueSmsForm */
/* @var $userTypeName string|null */

if ($userTypeName !== null) {
	$this->title = Yii::t('issue', 'Send SMS for Issue: {issue} - {userType}', [
		'issue' => $model->getIssue()->getIssueName(),
		'userType' => $userTypeName,
	]);
} else {
	$this->title = Yii::t('issue', 'Send SMS for Issue: {issue}', [
		'issue' => $model->getIssue()->getIssueName(),
	]);
}

$this->params['breadcrumbs'] = Breadcrumbs::issue($model->getIssue());
$this->params['breadcrumbs'][] = Yii::t('issue', 'Send SMS');
?>

<div class="issue-sms-send">
	<div class="lead-sms-push-form">
		<?php $form = ActiveForm::begin(['id' => 'issue-sms-push-form']) ?>

		<?= $model->isMultiple()
			? $form->field($model, 'phones')->widget(Select2::class, [
				'data' => $model->getPhonesData(),
				'options' => [
					'multiple' => true,
					'placeholder' => $model->getAttributeLabel('phones'),
				],
			]) : $form->field($model, 'phone')->dropDownList($model->getPhonesData()) ?>

		<?= $form->field($model, 'note_title')->textInput() ?>

		<?= $form->field($model, 'message')->textarea() ?>

		<?= $form->field($model, 'removeSpecialCharacters')->checkbox() ?>

		<?= $form->field($model, 'withOverwrite')->checkbox()->hint($model->getMessage()->getOverwriteSrc()) ?>


		<div class="form-group">
			<?= Html::submitButton(Yii::t('lead', 'Send'), ['class' => 'btn btn-success']) ?>
		</div>

		<?php ActiveForm::end() ?>
	</div>
</div>


