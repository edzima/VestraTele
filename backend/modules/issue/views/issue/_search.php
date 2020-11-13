<?php

use backend\modules\issue\models\search\IssueSearch;
use common\models\user\User;
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
					'data' => Worker::getSelectList([Worker::ROLE_TELEMARKETER, Worker::PERMISSION_ISSUE]),
					'options' => [
						'placeholder' => $model->getAttributeLabel('tele_id'),
					],
					'pluginOptions' => [
						'allowClear' => true,
					],
				]
			) ?>
		<?= $form->field($model, 'lawyer_id', ['options' => ['class' => 'col-md-4']])
			->widget(Select2::class, [
					'data' => Worker::getSelectList([Worker::ROLE_LAWYER, Worker::PERMISSION_ISSUE]),
					'options' => [
						'placeholder' => $model->getAttributeLabel('lawyer_id'),
					],
					'pluginOptions' => [
						'allowClear' => true,
					],
				]
			) ?>

		<?= Yii::$app->user->can(User::ROLE_ADMINISTRATOR) ?
			$form->field($model, 'parentId', ['options' => ['class' => 'col-md-4']])
				->widget(Select2::class, [
						'data' => Worker::getSelectList(),
						'options' => [
							'placeholder' => $model->getAttributeLabel('parentId'),
						],
						'pluginOptions' => [
							'allowClear' => true,
						],
					]
				) : '' ?>
	</div>

	<div class="row">
		<?= $form->field($model, 'excludedStages', ['options' => ['class' => 'col-md-8']])->widget(Select2::class, [
			'data' => $model->getStagesNames(),
			'options' => [
				'multiple' => true,
				'placeholder' => Yii::t('backend', 'Excluded stages'),
			],
			'pluginOptions' => [
				'allowClear' => true,
			],
			'showToggleAll' => false,
		]) ?>


		<?= $form->field($model, 'onlyDelayed', ['options' => ['class' => 'col-md-4']])->checkbox() ?>
	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('backend', 'Reset'), 'index', ['class' => 'btn btn-default']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
