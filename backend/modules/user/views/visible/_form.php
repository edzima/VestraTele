<?php

use common\models\user\UserVisible;
use common\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model UserVisible */
/* @var $form ActiveForm */
/* @var $users string[] */
?>

<div class="user-visible-form">

	<?php $form = ActiveForm::begin([
		'id' => 'user-visible-form',
	]); ?>

	<?= $form->field($model, 'user_id')->widget(
		Select2::class, [
			'data' => $users,
		]
	) ?>


	<?= $form->field($model, 'to_user_id')->widget(
		Select2::class, [
			'data' => $users,
		]
	) ?>

	<?= $form->field($model, 'status')->dropDownList(UserVisible::getStatusesNames()) ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
