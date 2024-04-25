<?php

use common\modules\lead\models\searches\LeadCostSearch;
use common\widgets\DateTimeWidget;
use kartik\number\NumberControl;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var LeadCostSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="lead-cost-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<div class="row">
		<?= $form->field($model, 'fromAt', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->widget(DateTimeWidget::class) ?>

		<?= $form->field($model, 'toAt', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->widget(DateTimeWidget::class) ?>
		<?= $form->field($model, 'valueMin', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->widget(NumberControl::class) ?>

		<?= $form->field($model, 'valueMax', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->widget(NumberControl::class) ?>
	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('lead', 'Reset'), ['index'], ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
