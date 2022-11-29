<?php

use backend\helpers\Html;
use backend\modules\issue\models\search\IssueSearch;
use common\models\user\User;
use common\widgets\address\AddressSearchWidget;
use common\widgets\DateWidget;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model IssueSearch */
/* @var $form yii\widgets\ActiveForm */

?>
<div id="issue-search" class="issue-search">

	<?php $form = ActiveForm::begin([
		'options' => [
			'data-pjax' => 1,
		],
		'id' => 'issue-search-form',
		'method' => 'get',
	]); ?>

	<div class="row">

		<?= $form->field($model, 'createdAtFrom', ['options' => ['class' => 'col-md-2']])
			->widget(DateWidget::class)
		?>

		<?= $form->field($model, 'createdAtTo', ['options' => ['class' => 'col-md-2']])
			->widget(DateWidget::class)
		?>


		<?= $form->field($model, 'signedAtFrom', ['options' => ['class' => 'col-md-2']])
			->widget(DateWidget::class)
		?>

		<?= $form->field($model, 'signedAtTo', ['options' => ['class' => 'col-md-2']])
			->widget(DateWidget::class)
		?>


		<?= $form->field($model, 'type_additional_date_at', ['options' => ['class' => 'col-md-2']])
			->widget(DateWidget::class)
		?>

		<?= $form->field($model, 'stage_change_at', ['options' => ['class' => 'col-md-2']])
			->widget(DateWidget::class)
		?>

	</div>
	<div class="row">

		<?= $form->field($model, 'signature_act', ['options' => ['class' => 'col-md-2 col-lg-1']])->textInput() ?>

		<?= $form->field($model, 'tele_id', ['options' => ['class' => 'col-md-3 col-lg-2']])
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

		<?= $form->field($model, 'onlyWithTelemarketers', ['options' => ['class' => 'col-md-2']])
			->dropDownList(Html::booleanDropdownList(), [
				'prompt' => Yii::t('common', 'All'),
			])
		?>
		<?= $form->field($model, 'lawyer_id', ['options' => ['class' => 'col-md-3 col-lg-2']])
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
			$form->field($model, 'parentId', ['options' => ['class' => 'col-md-3 col-lg-2']])
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

		<?= $form->field($model, 'excludedTypes', ['options' => ['class' => 'col-md-5 col-lg-4']])->widget(Select2::class, [
			'data' => IssueSearch::getIssueTypesNames(),
			'options' => [
				'multiple' => true,
				'placeholder' => $model->getAttributeLabel('excludedTypes'),
			],
			'pluginOptions' => [
				'allowClear' => true,
			],
		]) ?>

		<?= $form->field($model, 'excludedStages', ['options' => ['class' => 'col-md-5 col-lg-4']])->widget(Select2::class, [
			'data' => $model->getStagesNames(),
			'options' => [
				'multiple' => true,
				'placeholder' => $model->getAttributeLabel('excludedStages'),
			],
			'pluginOptions' => [
				'allowClear' => true,
			],
		]) ?>


		<?= $form->field($model, 'onlyDelayed', ['options' => ['class' => 'col-md-1']])->checkbox() ?>

		<?= Yii::$app->user->can(User::PERMISSION_PAY_PART_PAYED) ?
			$form->field($model, 'onlyWithPayedPay', ['options' => ['class' => 'col-md-2']])->checkbox()
			: ''
		?>

		<?= $model->scenario === IssueSearch::SCENARIO_ALL_PAYED
			? $form->field($model, 'onlyWithAllPayedPay', ['options' => ['class' => 'col-md-2']])->checkbox()
			: ''
		?>

		<?= Yii::$app->user->can(User::ROLE_BOOKKEEPER) ?
			$form->field($model, 'onlyWithSettlements', ['options' => ['class' => 'col-md-2']])->dropDownList(Html::booleanDropdownList(), [
				'prompt' => Yii::t('common', 'All'),
			])
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


	<div class="row">
		<?= $form->field($model, 'userName', ['options' => ['class' => 'col-md-2']])
			->textInput()
		?>

		<?= $form->field($model, 'tagsIds', ['options' => ['class' => 'col-md-8 col-lg-6']])->widget(Select2::class, [
			'data' => IssueSearch::getTagsNames(),
			'options' => [
				'multiple' => true,
				'placeholder' => Yii::t('issue', 'Tags'),
			],
			'pluginOptions' => [
				'allowClear' => true,
			],
			'showToggleAll' => false,
		]) ?>


		<?= Yii::$app->user->can(User::ROLE_BOOKKEEPER) ?
			$form->field($model, 'onlyWithClaims', ['options' => ['class' => 'col-md-2']])->dropDownList(Html::booleanDropdownList(), [
				'prompt' => Yii::t('common', 'All'),
			])
			: ''
		?>


	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('backend', 'Reset'),
			['index'], [
				'class' => 'btn btn-default',
			]) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
