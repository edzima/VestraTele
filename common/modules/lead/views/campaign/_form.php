<?php

use common\modules\lead\models\LeadCampaign;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadCampaign */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-campaign-form">

	<?php $form = ActiveForm::begin(
		['id' => 'lead-campaign-form']
	); ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
	
	<?= $form->field($model, 'owner_id')->textInput() ?>

	<?= $form->field($model, 'sort_index')->textInput() ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
