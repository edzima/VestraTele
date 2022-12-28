<?php

use backend\modules\issue\models\StageTypeForm;
use common\helpers\Html;
use common\widgets\ActiveForm;
use kartik\color\ColorInput;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model StageTypeForm */

$this->title = Yii::t('backend', 'Stage: {stage} for Type: {type}', [
	'stage' => $model->getModel()->getStageName(),
	'type' => $model->getModel()->getTypeName(),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issues'), 'url' => ['issue/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Stages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getModel()->getStageName(), 'url' => ['stage/view', 'id' => $model->getModel()->stage_id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Update');
?>
<div class="issue-stage-update">

	<div class="issue-stage-type-form">

		<?php
		$form = ActiveForm::begin(['id' => 'issue-stage-type-form']);
		?>

		<?= $form->field($model, 'stage_id')->widget(Select2::class, [
			'data' => $model->getStagesNames(),
		]) ?>

		<?= $form->field($model, 'type_id')->widget(Select2::class, [
			'data' => $model->getTypesNames(),
		]) ?>

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
