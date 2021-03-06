<?php

use common\helpers\Html;
use common\modules\lead\models\forms\LeadsUserForm;
use common\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this \yii\web\View */
/* @var $model LeadsUserForm */
?>

<div class="leads-user-form">

	<?php $form = ActiveForm::begin([
		'id' => 'leads-user-form',
	]); ?>

	<?= $form->field($model, 'leadsIds')->widget(Select2::class, [
		'data' => LeadsUserForm::getLeadsIds(),
		'options' => [
			'multiple' => true,
		],
	]) ?>

	<?= $form->field($model, 'userId')->widget(Select2::class, [
		'data' => LeadsUserForm::getUsersNames(),
	]) ?>

	<?= $form->field($model, 'type')->dropDownList(LeadsUserForm::getTypesNames()) ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
