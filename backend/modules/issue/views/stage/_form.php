<?php

use backend\helpers\Html;
use backend\modules\issue\models\IssueStageForm;
use common\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model IssueStageForm */
/* @var $form ActiveForm */

?>

<div class="issue-stage-form">


	<?php
	$form = ActiveForm::begin(['id' => 'issue-stage-form']);
	?>

	<div class="row">

		<?= $form->field($model, 'name', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->textInput(['maxlength' => true]) ?>

		<?= $form->field($model, 'short_name', [
			'options' => [
				'class' => 'col-md-2 col-lg-1',
			],
		])->textInput(['maxlength' => true]) ?>

		<?= $form->field($model, 'posi', [
			'options' => [
				'class' => 'col-md-1',
			],
		])->textInput(['maxlength' => true]) ?>

	</div>



	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
