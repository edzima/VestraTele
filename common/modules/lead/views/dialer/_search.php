<?php

use common\modules\lead\models\searches\LeadDialerSearch;
use common\widgets\DateTimeWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadDialerSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-dialer-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<div class="row">

		<?= $form->field($model, 'onlyToCall', [
			'options' => [
				'class' => 'col-md-2',
			],
		])->checkbox() ?>

		<?= $form->field($model, 'leadStatusNotForDialer', [
			'options' => [
				'class' => 'col-md-2',
			],
		])->checkbox() ?>

		<?= $form->field($model, 'leadSourceWithoutDialer', [
			'options' => [
				'class' => 'col-md-2',
			],
		])->checkbox() ?>


	</div>


	<div class="row">
		<?= $form->field($model, 'fromLastAt', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->widget(DateTimeWidget::class)
		?>

		<?= $form->field($model, 'toLastAt', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->widget(DateTimeWidget::class)
		?>


		<?= $form->field($model, 'kindOfType', [
			'options' => [
				'class' => [
					'col-md-3 col-lg-2',
				],
			],
		])->dropDownList(LeadDialerSearch::getKindOfTypes(), ['prompt' => Yii::t('lead', 'Select...')])
		?>

	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Search'), ['class' => 'btn btn-primary']) ?>

		<?= Html::a(Yii::t('lead', 'Reset'), 'index', ['class' => 'btn btn-default']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
