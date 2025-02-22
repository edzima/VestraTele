<?php

use backend\helpers\Html;
use backend\modules\issue\models\IssueStage;
use backend\modules\issue\models\search\IssueSearch;
use backend\modules\issue\Module;
use common\models\user\User;
use common\models\user\Worker;
use common\widgets\address\AddressSearchWidget;
use common\widgets\DateWidget;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model IssueSearch */
/* @var $form yii\widgets\ActiveForm */

?>
<div id="issue-search" class="issue-search collapse<?= $model->getIsLoad() ? ' in' : '' ?>">

	<?php $form = ActiveForm::begin([
		'options' => [
			'data-pjax' => 1,
		],
		'id' => 'issue-search-form',
		'method' => 'get',
		'action' => null,
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

		<?= $form->field($model, 'onlyWithTelemarketers', ['options' => ['class' => 'col-md-2 col-lg-1']])
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

		<?= Yii::$app->user->can(Worker::PERMISSION_ISSUE_SEARCH_PARENTS) ?
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

		<?= $form->field($model, 'stageDeadlineFromAt', ['options' => ['class' => 'col-md-2']])
			->widget(DateWidget::class)
		?>

		<?= $form->field($model, 'stageDeadlineToAt', ['options' => ['class' => 'col-md-2']])
			->widget(DateWidget::class)
		?>
	</div>

	<div class="row">

		<?= $form->field($model, 'excludedTypes', ['options' => ['class' => 'col-md-5 col-lg-4']])->widget(Select2::class, [
			'data' => $model->getIssueTypesNames(),
			'options' => [
				'multiple' => true,
				'placeholder' => $model->getAttributeLabel('excludedTypes'),
			],
			'pluginOptions' => [
				'allowClear' => true,
			],
		]) ?>

		<?= $form->field($model, 'excludedStages', ['options' => ['class' => 'col-md-5 col-lg-4']])->widget(Select2::class, [
			'data' => $model->getIssueStagesNames(),
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

		<?= Yii::$app->user->can(Worker::PERMISSION_ISSUE_CLAIM_TOTAL_SUM) ?
			$form->field($model, 'withClaimsSum', ['options' => ['class' => 'col-md-2']])->checkbox()
			: ''
		?>

		<?= $model->scenario === IssueSearch::SCENARIO_ALL_PAYED
			? $form->field($model, 'onlyWithAllPayedPay', ['options' => ['class' => 'col-md-2']])->checkbox()
			: ''
		?>

		<?= Yii::$app->user->can(Worker::PERMISSION_ISSUE_SEARCH_WITH_SETTLEMENTS) ?
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

		<?= $form->field($model, 'userType', ['options' => ['class' => 'col-md-2']])
			->dropDownList(IssueSearch::getIssueUserTypesNames(), [
				'prompt' => Yii::t('common', 'Select...'),
			])
		?>

		<?= $form->field($model, 'userName', ['options' => ['class' => 'col-md-2']])
			->textInput()
		?>


		<?= $form->field($model, 'withoutUserTypes', ['options' => ['class' => 'col-md-2']])
			->widget(Select2::class, [
				'data' => IssueSearch::getIssueUserTypesNames(),
				'options' => [
					'placeholder' => $model->getAttributeLabel('withoutUserTypes'),
				],
				'pluginOptions' => [
					'allowClear' => true,
					'multiple' => true,
				],
			])
		?>


		<?= Yii::$app->user->can(User::ROLE_BOOKKEEPER) ?
			$form->field($model, 'onlyWithClaims', ['options' => ['class' => 'col-md-2']])->dropDownList(Html::booleanDropdownList(), [
				'prompt' => Yii::t('common', 'All'),
			])
			: ''
		?>

		<?= $form->field($model, 'type_additional_date_from_at', ['options' => ['class' => 'col-md-2']])
			->widget(DateWidget::class)
		?>

		<?= $form->field($model, 'type_additional_date_to_at', ['options' => ['class' => 'col-md-2']])
			->widget(DateWidget::class)
		?>


	</div>

	<div class="row">


		<?= $form->field($model, 'note_stage_id', ['options' => ['class' => 'col-md-2']])
			->widget(Select2::class, [
				'data' => IssueStage::getStagesNames(),
				'pluginOptions' => [
					'placeholder' => '',
					'allowClear' => true,
				],
			])
		?>

		<?= $form->field($model, 'note_stage_change_from_at', ['options' => ['class' => 'col-md-2']])
			->widget(DateWidget::class)
		?>

		<?= $form->field($model, 'note_stage_change_to_at', [
			'options' => [
				'class' => 'col-md-2',
				'placeholder' => 'test',
			],
		])
			->widget(DateWidget::class)
		?>

	</div>
	<div class="row">
		<?= $form->field($model, 'tagsIds', ['options' => ['class' => 'col-md-5']])->widget(Select2::class, [
			'data' => IssueSearch::getTagsNames(),
			'options' => [
				'multiple' => true,
				'placeholder' => $model->getAttributeLabel('tagsIds'),
			],
			'pluginOptions' => [
				'allowClear' => true,
			],
			'showToggleAll' => true,
		]) ?>

		<?= $form->field($model, 'excludedTagsIds', ['options' => ['class' => 'col-md-5']])->widget(Select2::class, [
			'data' => IssueSearch::getTagsNames(),
			'options' => [
				'multiple' => true,
				'placeholder' => $model->getAttributeLabel('excludedTagsIds'),
			],
			'pluginOptions' => [
				'allowClear' => true,
			],
			'showToggleAll' => true,
		]) ?>


		<?= $form->field($model, 'withoutTags', ['options' => ['class' => 'col-md-2']])->checkbox() ?>

	</div>

	<div class="row">
		<?= $form->field($model, 'entity_agreement_details', [
			'options' => [
				'class' => 'col-md-3',
			],
		])->textInput() ?>
		<?= $form->field($model, 'entity_agreement_at', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->widget(DateWidget::class) ?>

		<?= $form->field($model, 'groupByIssueUserTypes', ['options' => ['class' => 'col-md-2']])
			->widget(Select2::class, [
				'data' => IssueSearch::getIssueUserTypesNames(),
				'pluginOptions' => [
					'prompt' => Yii::t('common', 'Select...'),
					'multiple' => true,
				],
			])
		?>


		<?= $form->field($model, 'details', [
			'options' => [
				'class' => 'col-md-4',
			],
		])->textInput() ?>
	</div>

	<div class="row">
		<?= $form->field($model, 'excludedEntity', ['options' => ['class' => 'col-md-4']])
			->widget(Select2::class, [
				'data' => $model->getEntityResponsibleNames(),
				'options' => [
					'multiple' => true,
					'placeholder' => $model->getAttributeLabel('excludedEntity'),
				],
				'pluginOptions' => [
					'allowClear' => true,
				],
				'showToggleAll' => true,
			]) ?>

		<?= Yii::$app->user->can(
			Module::PERMISSION_ISSUE_CHART
		)
			? $form->field($model, 'showChart')->checkbox()
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
