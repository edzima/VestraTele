<?php

use common\models\User;
use common\widgets\DateTimeWidget;
use frontend\models\IssueSearch;
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
		<?= Yii::$app->user->can(User::ROLE_AGENT) ? $form->field($model, 'onlyAsAgent', ['options' => ['class' => 'col-md-1']])->checkbox() : '' ?>
		<?= Yii::$app->user->can(User::ROLE_TELEMARKETER) ? $form->field($model, 'onlyAsTele', ['options' => ['class' => 'col-md-1']])->checkbox() : '' ?>
		<?= Yii::$app->user->can(User::ROLE_LAWYER) ? $form->field($model, 'onlyAsLawyer', ['options' => ['class' => 'col-md-1']])->checkbox() : '' ?>
	</div>

	<div class="form-group">
		<?= Html::submitButton('Szukaj', ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Reset', 'index', ['class' => 'btn btn-default']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
