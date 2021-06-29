<?php

use common\modules\lead\models\LeadUser;
use common\modules\lead\Module;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadUser */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-user-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'lead_id')->textInput() ?>

	<?= $form->field($model, 'user_id')->widget(Select2::class, [
		'data' => Module::userNames(),
	]) ?>

	<?= $form->field($model, 'type')->dropDownList(LeadUser::getTypesNames()) ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
