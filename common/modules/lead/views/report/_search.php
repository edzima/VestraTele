<?php

use common\modules\lead\models\searches\LeadReportSearch;
use common\widgets\DateWidget;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadReportSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-report-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<div class="row">

		<?= $form->field($model, 'from_at', [
			'options' => [
				'class' => [
					'col-md-3',
				],
			],
		])->widget(DateWidget::class)
		?>

		<?= $form->field($model, 'to_at', [
			'options' => [
				'class' => [
					'col-md-3',
				],
			],
		])->widget(DateWidget::class)
		?>
		<?= $form->field($model, 'lead_source_id', [
			'options' => [
				'class' => 'col-md-3',
			],
		])->widget(Select2::class, [
			'data' => $model->getSourcesNames(),
			'pluginOptions' => [
				'placeholder' => $model->getAttributeLabel('lead_source_id'),
				'allowClear' => true,
			],
		]) ?>

		<?= $form->field($model, 'lead_campaign_id', [
			'options' => [
				'class' => 'col-md-3',
			],
		])->widget(Select2::class, [
			'data' => $model->getCampaignNames(),
			'pluginOptions' => [
				'placeholder' => $model->getAttributeLabel('lead_campaign_id'),
				'allowClear' => true,
			],
		]) ?>


	</div>


	<?= $form->field($model, 'changedStatus')->checkbox() ?>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('lead', 'Reset'), ['index'], ['class' => 'btn btn-default']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
