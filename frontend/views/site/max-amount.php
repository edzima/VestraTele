<?php

use frontend\models\MaxAmountOfNonInterestFinancialCosts;
use kartik\number\NumberControl;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $model MaxAmountOfNonInterestFinancialCosts */

$this->title = Yii::t('frontend', 'Max amount of non interest financial costs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-max-amount">
	<h1><?= Html::encode($this->title) ?></h1>

	<div class="row">

		<?php $form = ActiveForm::begin(); ?>

		<?= $form->field($model, 'months', [
			'options' => [
				'class' => 'col-md-2',
			],
		])->widget(NumberControl::class) ?>

		<?= $form->field($model, 'totalLoanAmount', [
			'options' => [
				'class' => 'col-md-2',
			],
		])->widget(NumberControl::class) ?>

		<?= $model->getIsCalculate()
			? $form->field($model, 'valueText', ['options' => ['class' => 'text-success col-md-2']])
				->widget(NumberControl::class, [
					'readonly' => true,
				])
				->label('MPKK')
			: ''
		?>

		<div class="clearfix"></div>

		<div class="form-group col-md-12">
			<?= Html::submitButton(Yii::t('frontend', 'Calculate'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
		</div>

		<?php ActiveForm::end(); ?>
	</div>
</div>
