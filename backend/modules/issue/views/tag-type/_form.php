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

	<div class="row">
		<?= $form->field($model, 'name', [
			'options' => [
				'class' => 'col-md-3',
			],
		])->textInput(['maxlength' => true]) ?>

	</div>


	<div class="row">

		<?= $form->field($model, 'background', [
			'options' => [
				'class' => 'col-md-3',
			],
		])->widget(
			ColorInput::class
		) ?>

		<?= $form->field($model, 'color', [
			'options' => [
				'class' => 'col-md-3',
			],
		])->widget(
			ColorInput::class
		) ?>

		<?= $form->field($model, 'css_class', [
			'options' => [
				'class' => 'col-md-3',
			],
		])->textInput(['maxlength' => true]) ?>


		<?= $form->field($model, 'sort_order', [
			'options' => [
				'class' => 'col-md-3',
			],
		])->textInput() ?>

	</div>

	<div class="row">

		<?= $form->field($model, 'view_issue_position', [
			'options' => [
				'class' => 'col-md-4',
			],
		])->dropDownList(
			IssueTagType::getViewIssuePositionNames(),
			[
				'prompt' => Yii::t('common', 'Select...'),
			])
		?>


		<?= $form->field($model, 'issues_grid_position', [
			'options' => [
				'class' => 'col-md-4',
			],
		])->dropDownList(
			IssueTagType::getIssuesGridPositionNames(),
			[
				'prompt' => Yii::t('common', 'Select...'),
			])
		?>


		<?= $form->field($model, 'link_issues_grid_position', [
			'options' => [
				'class' => 'col-md-4',
			],
		])->dropDownList(
			IssueTagType::getLinkIssuesGridPositionNames(),
			[
				'prompt' => Yii::t('common', 'Select...'),
			])
		?>

	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
