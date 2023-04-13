<?php

use common\helpers\Html;
use common\modules\lead\models\searches\LeadMarketSearch;
use common\widgets\address\AddressSearchWidget;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadMarketSearch */
/* @var $form yii\widgets\ActiveForm */
/* @var $action string */
?>

<div class="lead-market-search">

	<?php $form = ActiveForm::begin([
		'method' => 'get',
		'action' => $action,
	]); ?>


	<?= $model->addressSearch !== null
		?
		AddressSearchWidget::widget([
			'form' => $form,
			'model' => $model->addressSearch,
		])
		: ''
	?>

	<div class="row">
		<?= $form->field($model, 'selfAssign', [
			'options' => [
				'class' => 'col-md-2',
			],
		])->dropDownList(Html::booleanDropdownList(), ['prompt' => Yii::t('common', 'All')]) ?>

		<?= $form->field($model, 'selfMarket', [
			'options' => [
				'class' => 'col-md-2',
			],
		])->dropDownList(
			Html::booleanDropdownList(), ['prompt' => Yii::t('common', 'All')])
		?>


		<?= $form->field($model, 'withAddress', [
			'options' => [
				'class' => 'col-md-2',
			],
		])->dropDownList(
			Html::booleanDropdownList(), ['prompt' => Yii::t('common', 'All')])
		?>


		<?= $form->field($model, 'withPhone', [
			'options' => [
				'class' => 'col-md-2',
			],
		])->dropDownList(
			Html::booleanDropdownList(), ['prompt' => Yii::t('common', 'All')])
		?>


		<?= $form->field($model, 'withoutArchive', [
			'options' => [
				'class' => 'col-md-2',
			],
		])->checkbox() ?>
	</div>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('backend', 'Reset'), $action, ['class' => 'btn btn-default']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
