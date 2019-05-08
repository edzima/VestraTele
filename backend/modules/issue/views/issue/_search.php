<?php

use common\models\issue\IssueSearch;
use common\models\User;
use kartik\select2\Select2;
use trntv\yii\datetime\DateTimeWidget;
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

		<?= $form->field($model, 'createdAtFrom', ['options' => ['class' => 'col-md-6']])
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

		<?= $form->field($model, 'createdAtTo', ['options' => ['class' => 'col-md-6']])
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
					'data' => User::getSelectList([User::ROLE_TELEMARKETER]),
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
					'data' => User::getSelectList([User::ROLE_LAYER]),
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
					'data' => User::getSelectList(),
					'options' => [
						'placeholder' => 'Struktury',
					],
					'pluginOptions' => [
						'allowClear' => true,
					],
				]
			) ?>
	</div>

	<?= $form->field($model, 'archived')->checkbox() ?>


	<div class="form-group">
		<?= Html::submitButton('Szukaj', ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Reset', 'index', ['class' => 'btn btn-default']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
