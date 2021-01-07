<?php

use common\models\user\User;
use common\widgets\DateWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\settlement\PayReceived */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pay-received-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'pay_id')->textInput() ?>

	<?= $form->field($model, 'user_id')->dropDownList(User::getSelectList(Yii::$app->authManager->getUserIdsByRole(User::PERMISSION_PAY_RECEIVED))) ?>

	<?= $form->field($model, 'date_at')
		->widget(DateWidget::class)
	?>

	<?= $form->field($model, 'transfer_at')
		->widget(DateWidget::class)
	?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('settlement', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
