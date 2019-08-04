<?php

use common\models\issue\IssueType;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\issue\IssueStage */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="issue-stage-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'short_name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'posi')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'typesIds')->widget(Select2::class, [
		'data' => ArrayHelper::map(IssueType::find()
			->select('id,name')
			->all(), 'id', 'name'),
		'options' => [
			'multiple' => true,
		],
	]) ?>

	<div class="form-group">
		<?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
