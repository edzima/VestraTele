<?php

use common\models\provision\ProvisionSearch;
use common\widgets\DateTimeWidget;
use kartik\select2\Select2;
use yii\bootstrap\Nav;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ProvisionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="provision-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>


	<?= Nav::widget([
		'items' => [
			[
				'label' => 'Poprzedni (' . date('Y-m', strtotime('last month')) . ')',
				'url' => [
					'index',
					Html::getInputName($model, 'dateFrom') => date('Y-m-d', strtotime('first day of last month')),
					Html::getInputName($model, 'dateTo') => date('Y-m-d', strtotime('last day of last month')),

				],
				'active' => $model->dateFrom === date('Y-m-d', strtotime('first day of last month'))
					&& $model->dateTo === date('Y-m-d', strtotime('last day of last month')),
			],
			[
				'label' => 'Obecny (' . date('Y-m') . ')',
				'url' => [
					'index',
					Html::getInputName($model, 'dateFrom') => date('Y-m-d', strtotime('first day of this month')),
					Html::getInputName($model, 'dateTo') => date('Y-m-d', strtotime('last day of this month')),

				],
				'active' => $model->dateFrom === date('Y-m-d', strtotime('first day of this month'))
					&& $model->dateTo === date('Y-m-d', strtotime('last day of this month')),
			],
			[
				'label' => 'Następny (' . date('Y-m', strtotime('next month')) . ')',
				'url' => [
					'index',
					Html::getInputName($model, 'dateFrom') => date('Y-m-d', strtotime('first day of next month')),
					Html::getInputName($model, 'dateTo') => date('Y-m-d', strtotime('last day of next month')),
				],
				'active' => $model->dateFrom === date('Y-m-d', strtotime('first day of next month'))
					&& $model->dateTo === date('Y-m-d', strtotime('last day of next month')),
			],
		],
		'options' => ['class' => 'nav-pills'],

	]) ?>

	<div class="form-group row">


		<?= $form->field($model, 'dateFrom', ['options' => ['class' => 'col-md-3']])
			->widget(DateTimeWidget::class,
				[
					'phpDatetimeFormat' => 'yyyy-MM-dd',
					'clientOptions' => [

						'allowInputToggle' => true,
						'sideBySide' => true,
						'widgetPositioning' => [
							'horizontal' => 'auto',
							'vertical' => 'auto',
						],
					],
				]) ?>

		<?= $form->field($model, 'dateTo', ['options' => ['class' => 'col-md-3']])
			->widget(DateTimeWidget::class,
				[
					'phpDatetimeFormat' => 'yyyy-MM-dd',
					'clientOptions' => [

						'allowInputToggle' => true,
						'sideBySide' => true,
						'widgetPositioning' => [
							'horizontal' => 'auto',
							'vertical' => 'auto',
						],
					],
				]) ?>
		<?= $form->field($model, 'to_user_id', ['options' => ['class' => 'col-md-3']])
			->widget(Select2::class, [
					'data' => $model->getToUsersList(),
					'options' => [
						'placeholder' => 'Agent',
					],
					'pluginOptions' => [
						'allowClear' => true,
					],
				]
			) ?>

		<?= $form->field($model, 'from_user_id', ['options' => ['class' => 'col-md-3']])
			->widget(Select2::class, [
					'data' => $model->getFromUserList(),
					'options' => [
						'placeholder' => 'Agent',
					],
					'pluginOptions' => [
						'allowClear' => true,
					],
				]
			) ?>


		<?= $form->field($model, 'hide_on_report', ['options' => ['class' => 'col-md-2']])->checkbox() ?>


	</div>

	<div class="form-group">
		<?= $form->field($model, 'payStatus')->dropDownList(ProvisionSearch::getPayStatusNames()) ?>
	</div>


	<div class="form-group">
		<?= Html::submitButton('Szukaj', ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Reset', 'index', ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
