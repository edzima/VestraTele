<?php

use backend\modules\issue\models\search\IssueSearch;
use common\models\user\User;
use common\widgets\address\AddressSearchWidget;
use common\widgets\DateWidget;
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
			->widget(DateWidget::class)
		?>
		<?= $form->field($model, 'createdAtTo', ['options' => ['class' => 'col-md-4']])
			->widget(DateWidget::class) ?>

		<?= $form->field($model, 'type_additional_date_at', ['options' => ['class' => 'col-md-4']])
			->widget(DateWidget::class) ?>

	</div>
	<div class="row">
		<?= $form->field($model, 'tele_id', ['options' => ['class' => 'col-md-4']])
			->widget(Select2::class, [
					'data' => IssueSearch::getTelemarketersNames(),
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
					'data' => IssueSearch::getLawyersNames(),
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
						'data' => User::getSelectList(Yii::$app->userHierarchy->getAllParentsIds()),
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
		<?= $form->field($model, 'excludedStages', ['options' => ['class' => 'col-md-7']])->widget(Select2::class, [
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

		<?= $form->field($model, 'signature_act', ['options' => ['class' => 'col-md-1']])->textInput() ?>

		<?= $form->field($model, 'onlyDelayed', ['options' => ['class' => 'col-md-4']])->checkbox() ?>

		<?= Yii::$app->user->can(User::PERMISSION_PAY_PART_PAYED) ?
			$form->field($model, 'onlyWithPayedPay', ['options' => ['class' => 'col-md-4']])->checkbox()
			: ''
		?>

	</div>

	<?= $model->addressSearch !== null
		? AddressSearchWidget::widget([
			'form' => $form,
			'model' => $model->addressSearch,
		])
		: ''
	?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('backend', 'Reset'), 'index', ['class' => 'btn btn-default']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
