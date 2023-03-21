<?php

use common\models\issue\IssueTag;
use common\models\issue\IssueTagType;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model IssueTag */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="issue-tag-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'type')->dropDownList(IssueTagType::getTypesNames(), [
		'prompt' => Yii::t('common', 'Select...'),
	]) ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'is_active')->checkbox() ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
