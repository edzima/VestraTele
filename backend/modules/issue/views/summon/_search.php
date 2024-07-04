<?php

use backend\modules\issue\models\search\SummonSearch;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model SummonSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="summon-search">

	<?php $form = ActiveForm::begin([
		'method' => 'get',
	]); ?>


	<div class="row">
		<?= $form->field($model, 'tagsIds', ['options' => ['class' => 'col-md-4']])
			->widget(Select2::class, [
				'data' => SummonSearch::getTagsNames(),
				'options' => [
					'multiple' => true,
					'placeholder' => $model->getAttributeLabel('tagsIds'),
				],
				'pluginOptions' => [
					'allowClear' => true,
				],
				'showToggleAll' => true,
			]) ?>

		<?= $form->field($model, 'excludedTagsIds', ['options' => ['class' => 'col-md-4']])
			->widget(Select2::class, [
				'data' => SummonSearch::getTagsNames(),
				'options' => [
					'multiple' => true,
					'placeholder' => $model->getAttributeLabel('excludedTagsIds'),
				],
				'pluginOptions' => [
					'allowClear' => true,
				],
				'showToggleAll' => true,
			]) ?>


		<?= $form->field($model, 'excludedIssueStagesIds', ['options' => ['class' => 'col-md-4']])
			->widget(Select2::class, [
				'data' => $model->getIssueStagesNames(),
				'options' => [
					'multiple' => true,
					'placeholder' => $model->getAttributeLabel('excludedIssueStagesIds'),
				],
				'pluginOptions' => [
					'allowClear' => true,
				],
				'showToggleAll' => true,
			]) ?>



	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Reset', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
