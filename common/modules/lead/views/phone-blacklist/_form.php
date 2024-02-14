<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\modules\lead\models\LeadPhoneBlacklist $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="lead-phone-blacklist-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'user_id')->textInput() ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
