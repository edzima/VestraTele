<?php

use backend\modules\issue\models\SummonForm;
use common\widgets\address\CitySimcInputWidget;
use common\widgets\DateTimeWidget;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model SummonForm */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="summon-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">

		<?= $form->field($model, 'issue_id', [
			'options' => [
				'class' => 'col-md-1',
			],
		])->textInput(['maxlength' => true]) ?>

		<?= $form->field($model, 'type', [
			'options' => [
				'class' => 'col-md-2',
			],
		])->dropDownList(SummonForm::getTypesNames()) ?>

		<?= !$model->getModel()->isNewRecord ? $form->field($model, 'status', [
			'options' => [
				'class' => 'col-md-3',
			],
		])->dropDownList(SummonForm::getStatusesNames()) : '' ?>
		<?= $form->field($model, 'term', [
			'options' => [
				'class' => 'col-md-2',
			],
		])->dropDownList(SummonForm::getTermsNames()) ?>



		<?= $form->field($model, 'contractor_id', ['options' => ['class' => 'col-md-4']])
			->widget(Select2::class, [
					'data' => $model->getContractors(),
				]
			) ?>

	</div>

	<div class="row">

		<?= $form->field($model, 'entity_id', [
			'options' => [
				'class' => 'col-md-3',
			],
		])->dropDownList(SummonForm::getEntityNames()) ?>

		<?= $form->field($model, 'city_id', [
			'options' => [
				'class' => 'col-md-4',
			],
		])->widget(CitySimcInputWidget::class) ?>
	</div>


	<?= $form->field($model, 'title')->textarea(['maxlength' => true]) ?>


	<div class="row">

		<?= $form->field($model, 'start_at', [
			'options' => [
				'class' => 'col-md-3',
			],
		])
			->widget(DateTimeWidget::class, [
				'phpDatetimeFormat' => 'yyyy-MM-dd',
			]) ?>

		<?= $form->field($model, 'realize_at', [
			'options' => [
				'class' => 'col-md-3',
			],
		])
			->widget(DateTimeWidget::class) ?>


		<?= $model->getModel()->isRealized() ? $form->field($model, 'realized_at', [
			'options' => [
				'class' => 'col-md-3',
			],
		])
			->widget(DateTimeWidget::class) : '' ?>

	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('common', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
