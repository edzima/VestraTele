<?php

use common\models\issue\IssueTagType;
use kartik\color\ColorInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model IssueTagType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="issue-tag-type-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'background')->widget(
		ColorInput::class
	) ?>

	<?= $form->field($model, 'color')->widget(
		ColorInput::class
	) ?>

	<?= $form->field($model, 'css_class')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'view_issue_position')->dropDownList(
		IssueTagType::getViewIssuePositionNames(),
		[
			'prompt' => Yii::t('common', 'Select...'),
		])
	?>


	<?= $form->field($model, 'issues_grid_position')->dropDownList(
		IssueTagType::getIssuesGridPositionNames(),
		[
			'prompt' => Yii::t('common', 'Select...'),
		])
	?>

	<?= $form->field($model, 'sort_order')->textInput() ?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
