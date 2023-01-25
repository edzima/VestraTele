<?php

use common\modules\lead\models\LeadMarketUser;
use common\widgets\DateWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadMarketUser */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-market-user-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'status')->dropDownList(LeadMarketUser::getStatusesNames()) ?>

	<?= $form->field($model, 'details')->textarea() ?>

	<?= $form->field($model, 'reserved_at')->widget(
		DateWidget::class
	) ?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
