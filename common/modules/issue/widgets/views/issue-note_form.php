<?php

use common\helpers\Html;
use common\models\issue\IssueNoteForm;
use common\widgets\ActiveForm;
use common\widgets\DateTimeWidget;
use kartik\select2\Select2;
use yii\web\JsExpression;

/* @var $this \yii\web\View */
/* @var $model IssueNoteForm */
/* @var array $options */
/* @var $titleUrl string */
/* @var $descriptionUrl string */

?>

<div class="issue-note-form">

	<?php $form = ActiveForm::begin($options); ?>

	<div class="row">
		<?= $form->field($model, 'publish_at', [
			'options' => ['class' => 'col-md-2'],
		])->widget(DateTimeWidget::class) ?>

		<?= $form->field($model, 'is_pinned', [
			'options' => ['class' => 'col-md-1'],
		])->checkbox() ?>

		<?= $form->field($model, 'is_template', [
			'options' => ['class' => 'col-md-1'],
		])->checkbox() ?>
	</div>


	<?= $form->field($model, 'title')->widget(Select2::class, [
		'options' => ['placeholder' => Yii::t('issue', 'Search for a title ...')],
		'pluginEvents' => [
			'select2:open' => new JsExpression('function(e){
				let searchInput = document.getElementsByClassName("select2-search__field")[0];
				searchInput.value = e.currentTarget.value;
			}'),
		],
		'pluginOptions' => [
			'tags' => true,
			'minimumInputLength' => 3,
			'ajax' => [
				'delay' => 250,
				'url' => $titleUrl,
				'dataType' => 'json',
				'data' => new JsExpression('function(params) { return {q:params.term}; }'),
				'templateResult' => new JsExpression('function(note) { return note.text; }'),
				'templateSelection' => new JsExpression('function (note) { return note.text; }'),
			],
		],
	])
	?>

	<?= $form->field($model, 'description', [
		'options' => [
			'class' => 'select-text-area-field',
		],
	])->widget(Select2::class, [
		'options' => [
			'placeholder' => Yii::t('issue', 'Search for a description ...'),
			'class' => 'select-text-area',
		],
		'pluginEvents' => [
			'select2:open' => new JsExpression('function(e){
				let searchInput = document.getElementsByClassName("select2-search__field")[0];
				searchInput.value = e.currentTarget.value;
			}'),
		],
		'pluginOptions' => [
			'allowClear' => true,
			'minimumInputLength' => 3,
			'tags' => true,
			'ajax' => [
				'delay' => 250,
				'url' => $descriptionUrl,
				'dataType' => 'json',
				'data' => new JsExpression('function(params) { return {q:params.term}; }'),
				'templateResult' => new JsExpression('function(note) { return note.text; }'),
				'templateSelection' => new JsExpression('function (note) { return note.text; }'),
			],
		],
	])
	?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('common', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

<style>
	.select-text-area-field .select2-selection--single {
		height: 102px;
	}

	.select-text-area-field .select2-selection--single .select2-selection__arrow {
		height: 100%;
	}

	.select-text-area-field .select2-selection--single .select2-selection__rendered {
		white-space: normal;
	}
</style>
