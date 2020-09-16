<?php

use common\models\issue\IssueSearch;
use common\models\user\Worker;
use common\widgets\DateTimeWidget;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model IssueSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div id="issue-search" class="issue-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<div class="row">

		<?= $form->field($model, 'createdAtFrom', ['options' => ['class' => 'col-md-4']])
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

		<?= $form->field($model, 'createdAtTo', ['options' => ['class' => 'col-md-4']])
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

		<?= $form->field($model, 'accident_at', ['options' => ['class' => 'col-md-4']])
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

	</div>
	<div class="row">
		<?= $form->field($model, 'tele_id', ['options' => ['class' => 'col-md-4']])
			->widget(Select2::class, [
					'data' => Worker::getSelectList([Worker::ROLE_TELEMARKETER, Worker::ROLE_ISSUE]),
					'options' => [
						'placeholder' => 'Telemarketer',
					],
					'pluginOptions' => [
						'allowClear' => true,
					],
				]
			) ?>
		<?= $form->field($model, 'lawyer_id', ['options' => ['class' => 'col-md-4']])
			->widget(Select2::class, [
					'data' => Worker::getSelectList([Worker::ROLE_LAWYER, Worker::ROLE_ISSUE]),
					'options' => [
						'placeholder' => 'Prawnik',
					],
					'pluginOptions' => [
						'allowClear' => true,
					],
				]
			) ?>

		<?= $form->field($model, 'childsId', ['options' => ['class' => 'col-md-4']])
			->widget(Select2::class, [
					'data' => Worker::getSelectList(),
					'options' => [
						'placeholder' => 'Struktury',
					],
					'pluginOptions' => [
						'allowClear' => true,
					],
				]
			) ?>
	</div>

	<div class="row">
		<?= $form->field($model, 'disabledStages', ['options' => ['class' => 'col-md-8']])->widget(Select2::class, [
			'data' => $model->getStagesNames(),
			'options' => [
				'multiple' => true,
				'placeholder' => 'Wykluczone etapy',

			],
			'pluginOptions' => [
				'allowClear' => true,
			],
			'showToggleAll' => false,
		]) ?>


		<?= $form->field($model, 'onlyDelayed', ['options' => ['class' => 'col-md-4']])->checkbox() ?>
	</div>


	<div class="form-group">
		<?= Html::submitButton('Szukaj', ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Reset', 'index', ['class' => 'btn btn-default']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
