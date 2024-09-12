<?php

use common\helpers\Html;
use common\modules\lead\models\forms\LeadCampaignForm;
use common\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\View;

/* @var $this View */
/* @var $model LeadCampaignForm */
?>

<div class="leads-user-form">

	<?php $form = ActiveForm::begin([
		'id' => 'leads-campaign-form',
	]); ?>

	<?= Html::hiddenInput('leadsIds', implode(',', $model->leadsIds)) ?>


	<?= $form->field($model, 'campaignId')->widget(Select2::class, [
		'data' => $model->getCampaignNames(),
		'pluginOptions' => [
			'allowClear' => true,
			'placeholder' => Yii::t('lead', 'Select...'),
		],
	]) ?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
