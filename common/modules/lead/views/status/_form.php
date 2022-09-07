<?php

use common\modules\lead\models\LeadStatus;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadStatus */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-status-form">

	<?php $form = ActiveForm::begin([
		'id' => 'lead-status-form',
	]); ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'market_status')->dropDownList(LeadStatus::getMarketStatusesNames(), [
		'prompt' => Yii::t('lead', 'Select...'),
	]) ?>

	<?= $form->field($model, 'market_status_same_contacts')->checkbox() ?>


	<?= $form->field($model, 'not_for_dialer')->checkbox() ?>

	<?= $form->field($model, 'short_report')->checkbox() ?>

	<?= $form->field($model, 'show_report_in_lead_index')->checkbox() ?>

	<?= $form->field($model, 'sort_index')->textInput() ?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
