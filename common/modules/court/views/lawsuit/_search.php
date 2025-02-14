<?php

use common\helpers\Html;
use common\modules\court\models\search\LawsuitSearch;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var LawsuitSearch $model */
/** @var yii\widgets\ActiveForm $form */
/** @var bool $withSPI */
?>

<div class="court-lawsuit-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<div class="row">
		<?= $withSPI
			? $form->field($model, 'spiToConfirm', [
				'options' => [
					'class' => 'col-md-2 col-lg-2',
				],
			])->dropDownList(Html::booleanDropdownList(), [
				'prompt' => Yii::t('common', 'All'),
			])
			: ''
		?>

		<?= $form->field($model, 'onlyWithResult', [
			'options' => [
				'class' => 'col-md-2 col-lg-1',
			],
		])->dropDownList(Html::booleanDropdownList(), [
			'prompt' => Yii::t('common', 'All'),
		]) ?>
		<?= $form->field($model, 'court_type', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->dropDownList(LawsuitSearch::getCourtTypeNames(), [
			'prompt' => Yii::t('common', '--- Select ---'),
		]) ?>


		<?= $form->field($model, 'details', [
			'options' => [
				'class' => 'col-md-4 col-lg-3',
			],
		])->textInput() ?>

		<?= $form->field($model, 'is_appeal', [
			'options' => [
				'class' => 'col-md-2 col-lg-2',
			],
		])->checkbox() ?>


	</div>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('common', 'Reset'), ['index'], ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
