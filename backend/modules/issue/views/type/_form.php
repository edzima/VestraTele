<?php

use common\models\issue\IssueType;
use common\modules\lead\models\LeadType;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model IssueType */
/* @var $form ActiveForm */
?>

<div class="issue-type-form">

	<?php $form = ActiveForm::begin([
		'id' => 'issue-type-form',
	]); ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'short_name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'lead_type_id')->dropDownList(LeadType::getNamesWithDescription()) ?>

	<?= $form->field($model, 'vat')->textInput() ?>

	<?= $form->field($model, 'meet')->checkbox() ?>

	<?= $form->field($model, 'with_additional_date')->checkbox() ?>

	<div class="form-group">
		<?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
