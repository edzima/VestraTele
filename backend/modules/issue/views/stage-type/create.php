<?php

use backend\modules\issue\models\IssueStage;
use backend\modules\issue\models\StageTypeForm;
use common\helpers\Html;
use common\models\issue\IssueType;
use common\widgets\ActiveForm;
use kartik\color\ColorInput;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model StageTypeForm */
/* @var $type IssueType|null */
/* @var $stage IssueStage|null */

if ($stage && $type) {
	$this->title = Yii::t('backend', 'Link: {stage} with Type: {type}', [
		'stage' => $stage->name,
		'type' => $type->name,
	]);
} else {
	if ($type !== null) {
		$this->title = Yii::t('backend', 'Link: {type} with Stage', [
			'type' => $type->name,
		]);
	} else {
		$this->title = Yii::t('backend', 'Link: {stage} with Type', [
			'stage' => $stage->name,
		]);
	}
}

$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issues'), 'url' => ['issue/index']];
if ($type) {
	$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Types'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $type->name, 'url' => ['type/view', 'id' => $type->id]];
}
if ($stage) {
	$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Stages'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $stage->name, 'url' => ['stage/view', 'id' => $stage->id]];
}
$this->params['breadcrumbs'][] = Yii::t('backend', 'Link');
?>
<div class="issue-stage-type-create">

	<div class="issue-stage-type-form">


		<?php
		$form = ActiveForm::begin(['id' => 'issue-stage-type-form']);
		?>

		<?= $type === null
			? $form->field($model, 'type_id')->widget(Select2::class, [
				'data' => $model->getTypesNames(),
			])
			: ''
		?>

		<?= $stage === null
			? $form->field($model, 'stage_id')->widget(Select2::class, [
				'data' => $model->getStagesNames(),
			]) : '' ?>

		<?= $form->field($model, 'days_reminder')->textInput(['maxlength' => true]) ?>

		<?= $form->field($model, 'calendar_background')->widget(
			ColorInput::class
		) ?>


		<div class="form-group">
			<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
		</div>

		<?php ActiveForm::end(); ?>

	</div>


</div>
