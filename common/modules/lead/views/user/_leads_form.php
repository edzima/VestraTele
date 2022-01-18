<?php

use common\helpers\Html;
use common\modules\lead\models\forms\LeadsUserForm;
use common\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\View;

/* @var $this View */
/* @var $model LeadsUserForm */
?>

<div class="leads-user-form">

	<?php $form = ActiveForm::begin([
		'id' => 'leads-user-form',
	]); ?>


	<?= $form->field($model, 'userId')->widget(Select2::class, [
		'data' => LeadsUserForm::getUsersNames(),
	]) ?>

	<?= $form->field($model, 'type')->dropDownList($model->getTypesNames()) ?>

	<?= $form->field($model, 'sendEmail')->checkbox() ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
