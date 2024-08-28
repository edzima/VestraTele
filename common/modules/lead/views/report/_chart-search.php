<?php

use common\helpers\Html;
use common\modules\lead\models\searches\LeadChartReportSearch;
use common\modules\lead\models\searches\LeadReportSearch;
use common\widgets\ActiveForm;
use common\widgets\DateWidget;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model LeadChartReportSearch */
/* @var $form yii\widgets\ActiveForm */
/* @var $action array */
?>

<div class="lead-report-search">

	<?php $form = ActiveForm::begin([
		'action' => ['chart'],
		'method' => 'get',
	]); ?>

	<div class="row">


		<?= $form->field($model, 'from_at', [
			'options' => [
				'class' => [
					'col-md-3 col-lg-2',
				],
			],
		])->widget(DateWidget::class)
		?>

		<?= $form->field($model, 'to_at', [
			'options' => [
				'class' => [
					'col-md-3 col-lg-2',
				],
			],
		])->widget(DateWidget::class)
		?>

		<?= $model->scenario === LeadReportSearch::SCENARIO_OWNER
			? $form->field($model, 'onlySelf', [
				'options' => [
					'class' => 'col-md-2 col-lg-1',
				],
			])->checkbox()
			: ''
		?>


	</div>

	<div class="row">
		<?= $form->field($model, 'old_status_id', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->widget(Select2::class, [
			'data' => $model->getLeadStatusNames(),
			'pluginOptions' => [
				'multiple' => true,
			],
		])
		?>
		<?= $form->field($model, 'status_id', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->widget(Select2::class, [
			'data' => $model->getLeadStatusNames(),
			'pluginOptions' => [
				'multiple' => true,
			],
		])
		?>
		<?= $form->field($model, 'lead_status_id', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->widget(Select2::class, [
			'data' => $model->getLeadStatusNames(),
			'pluginOptions' => [
				'multiple' => true,
			],
		])
		?>


		<?= $form->field($model, 'changedStatus', [
			'options' => [
				'class' => 'col-md-2 col-lg-1',
			],
		])->checkbox() ?>

		<?= $form->field($model, 'groupLeadStatus', [
			'options' => [
				'class' => 'col-md-2 col-lg-1',
			],
		])->checkbox() ?>
	</div>

	<div class="row">
		<?= $model->scenario !== LeadReportSearch::SCENARIO_OWNER
			? $form->field($model, 'owner_id', [
				'options' => [
					'class' => 'col-md-6 col-lg-4',
				],
			])->widget(Select2::class, [
				'data' => $model->getOwnersNames(),
				'pluginOptions' => [
					'multiple' => true,
				],
			])
			: ''
		?>

		<?= $form->field($model, 'lead_type_id', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->widget(Select2::class, [
			'data' => $model->leadTypesNames(),
			'pluginOptions' => [
				'multiple' => true,
			],
		])
		?>
	</div>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('lead', 'Reset'), ['chart'], ['class' => 'btn btn-default']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
