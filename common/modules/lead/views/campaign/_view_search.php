<?php

use common\modules\lead\models\searches\LeadCampaignCostSearch;
use common\widgets\DateWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadCampaignCostSearch */
/* @var $form yii\widgets\ActiveForm */
$id = $model->campaignIds[array_key_first($model->campaignIds)];
?>

<div class="lead-campaign-cost-search">

	<?php $form = ActiveForm::begin([
		'method' => 'get',
	]); ?>

	<div class="row">
		<?= $form->field($model, 'fromAt', [
			'options' => [
				'class' => 'col-md-4',
			],
		])->widget(DateWidget::class) ?>

		<?= $form->field($model, 'toAt', [
			'options' => [
				'class' => 'col-md-4',
			],
		])->widget(DateWidget::class) ?>
	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('lead', 'Reset'), [
			'view', 'id' => $id,
		], ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
