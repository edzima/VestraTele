<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\lead\models\LeadDialer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-dialer-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'lead_id')->textInput() ?>

	<?= $form->field($model, 'type_id')->textInput() ?>

	<?= $form->field($model, 'priority')->textInput() ?>

	<?= $form->field($model, 'created_at')->textInput() ?>

	<?= $form->field($model, 'updated_at')->textInput() ?>

	<?= $form->field($model, 'dialer_config')->textarea(['rows' => 6]) ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
