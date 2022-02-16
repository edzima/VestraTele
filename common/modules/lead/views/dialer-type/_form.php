<?php

use common\models\user\User;
use common\modules\lead\models\LeadDialerType;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\lead\models\LeadDialerType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-dialer-type-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'status')->dropDownList(LeadDialerType::getStatusesNames()) ?>

	<?= $form->field($model, 'user_id')->widget(Select2::class, [
		'data' => User::getSelectList(
			User::getAssignmentIds([User::PERMISSION_LEAD_DIALER])
		),
	]) ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
