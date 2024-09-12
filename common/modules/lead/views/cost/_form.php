<?php

use common\modules\lead\models\forms\LeadCostForm;
use common\widgets\DateWidget;
use kartik\number\NumberControl;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var LeadCostForm $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="lead-cost-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">
		<?= $form->field($model, 'campaign_id', [
			'options' => [
				'class' => 'col-md-6 col-lg-4',
			],
		])->widget(
			Select2::class, [
				'data' => $model->getCampaignNames(),
			]
		) ?>
	</div>
	<div class="row">


		<?= $form->field($model, 'date_at', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->widget(
			DateWidget::class,
		) ?>

		<?= $form->field($model, 'value', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
				'autofocus' => true,
			],
		])->widget(NumberControl::class, ['displayOptions' => ['autofocus' => true]]) ?>
	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
