<?php

use common\models\issue\IssueType;
use common\models\issue\Provision;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model IssueType */
/* @var $form ActiveForm */
?>

<div class="issue-type-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'short_name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'provision_type')->dropDownList(Provision::getTypesNames()) ?>

	<?= $form->field($model, 'vat')->textInput() ?>

	<?= $form->field($model, 'meet')->checkbox() ?>

	<div class="form-group">
		<?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
