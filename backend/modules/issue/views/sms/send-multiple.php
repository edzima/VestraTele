<?php

use backend\helpers\Html;
use backend\modules\issue\models\IssuesMultipleSmsForm;
use common\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\View;

/* @var $this View */
/* @var $model IssuesMultipleSmsForm */

$this->title = Yii::t('issue', 'Send SMS to Issues: {count}', ['count' => count($model->ids)]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issues'), 'url' => ['issue/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="issue-sms-send-multiple">
	<?php $form = ActiveForm::begin([
		'id' => 'issues-sms-multiple-form',
	]) ?>

	<?= Html::hiddenInput('ids', implode(',', $model->ids)) ?>


	<?= $form->field($model, 'userTypes')->widget(Select2::class, [
		'data' => IssuesMultipleSmsForm::getUsersTypesNames(),
		'options' => [
			'multiple' => true,
		],
	]) ?>

	<?= $form->field($model, 'message')->textarea() ?>

	<?= $form->field($model, 'removeSpecialCharacters')->checkbox() ?>

	<?= $form->field($model, 'withOverwrite')->checkbox()->hint($model->getMessage()->getOverwriteSrc()) ?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('common', 'Send SMS'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end() ?>
</div>
