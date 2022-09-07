<?php

use common\modules\lead\models\LeadMarketUser;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadMarketUser */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-market-user-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'status')->textInput() ?>

	<?= $form->field($model, 'details')->textarea() ?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
