<?php

use common\modules\lead\models\forms\LeadSourceForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadSourceForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-source-form">

	<?php $form = ActiveForm::begin(
		['id' => 'lead-source-form']
	); ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'type_id')->dropDownList(LeadSourceForm::getTypesNames()) ?>

	<?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'owner_id')->dropDownList(LeadSourceForm::getUsersNames()) ?>

	<?= $form->field($model, 'sort_index')->textInput() ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
