<?php

use backend\modules\provision\models\ProvisionUserForm;
use common\models\provision\ProvisionUser;
use yii\bootstrap\ActiveForm;
use yii\web\View;

/* @var $this View */
/* @var $formModel ProvisionUserForm */
/* @var $model ProvisionUser */
/* @var $form ActiveForm */
/* @var $index int */
$subName = "[$index]";

?>

<div class="row provision-user-part-form">
	<?= $form->field($model, $subName . 'type_id', ['options' => ['class' => 'col-md-12']])->dropDownList($formModel->getTypesNames(), ['disabled' => true]) ?>
	<?= $form->field($model, $subName . 'value', [
		'options' => ['class' => 'col-md-12 ' . ($model->isNewRecord ? 'has-warning' : '')],
		'template' => '<div class="input-group form-group"><span class="input-group-addon"><i class="fa '
			. ($model->type->is_percentage ? 'fa-percent' : 'fa-dollar')
			. '"></i></span>{input}</div>',
	])
		->textInput(['maxlength' => true])
	?>
	<?= $form->field($model, $subName . 'from_user_id')->hiddenInput()->label(false) ?>
	<?= $form->field($model, $subName . 'to_user_id')->hiddenInput()->label(false) ?>
</div>





