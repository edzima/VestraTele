<?php

use backend\helpers\Url;
use backend\modules\issue\models\search\IssueSearch;
use backend\modules\issue\widgets\StageChangeButtonDropdown;
use backend\widgets\GridView;
use common\helpers\Html;
use common\models\issue\Issue;
use common\models\user\Worker;
use common\modules\issue\IssueNoteColumn;
use common\modules\issue\widgets\IssueClaimCompanyColumn;
use common\modules\issue\widgets\IssuePaysColumnWidget;
use common\modules\issue\widgets\IssueSummonsColumn;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\CustomerDataColumn;
use common\widgets\grid\DataColumn;
use common\widgets\grid\IssueColumn;
use common\widgets\grid\IssueTypeColumn;
use common\widgets\grid\SerialColumn;
use kartik\grid\CheckboxColumn;
use kartik\select2\Select2;
use yii\bootstrap\Alert;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $searchModel IssueSearch */
/* @var $dataProvider ActiveDataProvider */

//@todo remove this after migrate BS4 (add data-boundary="viewport")
//@see https://stackoverflow.com/questions/26018756/bootstrap-button-drop-down-inside-responsive-table-not-visible-because-of-scroll#answer-51992907
$this->registerJs("$('.table-responsive').on('show.bs.dropdown', function () {
	     $('.table-responsive').css('overflow', 'inherit' );
		});

		$('.table-responsive').on('hide.bs.dropdown', function () {
            $('.table-responsive').css( 'overflow', 'auto' );
	})"
);
?>


<?= GridView::widget([
	'id' => 'issues-list',
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel->isArchiveScenario() ? null : $searchModel,
	'rowOptions' => static function (Issue $issue): array {
		if ($issue->hasDelayedStage()) {
			return [
				'style' => [
					'background-color' => '#f5c6cb',
				],
			];
		}
		return [];
	},
	'emptyText' => $searchModel->hasExcludedArchiveStage()
		? Alert::widget([
			'body' =>
				Html::a(Yii::t('issue', 'The archive is excluded. Matching Issues found in it: {count}.', [
					'count' => $searchModel->getTotalCountWithArchive(),
				]), ['archive', 'customerName' => $searchModel->customerName]),
			'options' => [
				'class' => 'alert-warning text-center mb-0',
			],
		])
		: null,
	'columns' => [
		!$searchModel->isArchiveScenario() && Yii::$app->user->can(Worker::PERMISSION_MULTIPLE_SMS)
			? [
			'class' => CheckboxColumn::class,
		]
			: [
			'visible' => false,
		],
		[
			'class' => SerialColumn::class,
			'width' => '40px',
		],
		[
			'class' => IssueColumn::class,
		],
		[
			'attribute' => 'signature_act',
			'visible' => !empty($searchModel->signature_act),
			'options' => [
				'style' => 'width:180px',
			],
		],

		[
			'class' => DataColumn::class,
			'filterType' => GridView::FILTER_SELECT2,
			'attribute' => 'agent_id',
			'value' => 'agent',
			'label' => $searchModel->getAttributeLabel('agent_id'),
			'filter' => $searchModel->getAgentsNames(),
			'filterWidgetOptions' => [
				'pluginOptions' => [
					'allowClear' => true,
				],
				'options' => [
					'placeholder' => $searchModel->getAttributeLabel('agent_id'),
				],
			],
			'ellipsis' => true,
			'options' => [
				'style' => 'width:10%',
			],
		],
		[
			'class' => IssueTypeColumn::class,
			'attribute' => 'type_id',
		],
		[
			'class' => CustomerDataColumn::class,
			'value' => 'customer.fullName',
			'attribute' => 'customerName',
		],
		[
			'class' => DataColumn::class,
			'attribute' => 'stage_id',
			'filter' => $searchModel->getStagesNames(),
			'value' => static function (Issue $model): string {
				return StageChangeButtonDropdown::widget([
					'model' => $model,
					'label' => $model->stage->name,
					'containerOptions' => [
						'class' => 'd-inline-flex',
					],
					'returnUrl' => Url::to('/issue/issue/index'),
					'options' => [
						'class' => 'btn btn-default btn-sm',
						'title' => Yii::t('backend', 'Change Stage'),
						'aria-label' => Yii::t('backend', 'Change Stage'),
						'data-pjax' => 0,
					],
				]);
			},
			'options' => [
				'style' => 'width:250px',
			],
			'format' => 'raw',
			'contentBold' => true,
			'contentCenter' => true,
			'filterInputOptions' => [
				'placeholder' => Yii::t('issue', 'Stage'),
			],
			'filterType' => GridView::FILTER_SELECT2,
			'filterWidgetOptions' => [
				'size' => Select2::SIZE_SMALL,
				'pluginOptions' => [
					'allowClear' => true,
					'dropdownAutoWidth' => true,
				],
			],
		],
		[
			'class' => DataColumn::class,
			'attribute' => 'entity_responsible_id',
			'filter' => IssueSearch::getEntityNames(),
			'filterType' => GridView::FILTER_SELECT2,
			'filterWidgetOptions' => [
				'pluginOptions' => [
					'allowClear' => true,
				],
				'options' => [
					'placeholder' => $searchModel->getAttributeLabel('entity_responsible_id'),
				],
			],
			'ellipsis' => true,
			'value' => 'entityResponsible.name',
			'options' => [
				'style' => 'width:140px',
			],
		],

		[
			'class' => DataColumn::class,
			'attribute' => 'stage_change_at',
			'format' => 'date',
			'contentBold' => true,
			'options' => [
				'style' => 'width:95px',
			],
		],
		[
			'class' => DataColumn::class,
			'attribute' => 'stage_deadline_at',
			'format' => 'date',
			'options' => [
				'style' => 'width:95px',
			],
		],
		[
			'class' => DataColumn::class,
			'attribute' => 'signing_at',
			'format' => 'date',
			'options' => [
				'style' => 'width:90px',
			],
			'visible' => !empty($searchModel->signedAtFrom) || !empty($searchModel->signedAtTo),
		],
		[
			'class' => DataColumn::class,
			'attribute' => 'created_at',
			'format' => 'date',
			'contentBold' => true,
			'options' => [
				'style' => 'width:90px',
			],
		],
		[
			'class' => DataColumn::class,
			'attribute' => 'updated_at',
			'format' => 'date',
			'contentBold' => true,
			'options' => [
				'style' => 'width:99px',
			],
		],
		[
			'class' => IssueClaimCompanyColumn::class,
			'attribute' => 'claimCompanyTryingValue',
		],
		[
			'class' => IssueNoteColumn::class,
		],
		[
			'class' => IssueSummonsColumn::class,
		],
		[
			'class' => IssuePaysColumnWidget::class,
			'visible' => Yii::$app->user->can(Worker::PERMISSION_PAY_ALL_PAID),
		],
		[
			'class' => ActionColumn::class,
			'template' => '{installment} {link} {note} {sms} {view} {update} {delete}',
			'buttons' => [
				'installment' => static function (string $url, Issue $model): string {
					return Html::a('<i class="fa fa-money" aria-hidden="true"></i>',
						['/settlement/cost/create-installment', 'id' => $model->id, 'user_id' => $model->agent->id],
						[
							'title' => Yii::t('settlement', 'Create Installment'),
							'aria-label' => Yii::t('settlement', 'Create Installment'),
						]
					);
				},
				'link' => static function (string $url, Issue $model) {
					return Html::a('<span class="glyphicon glyphicon-paperclip"></span>',
						['/issue/relation/create', 'id' => $model->id],
						[
							'title' => Yii::t('backend', 'Link'),
							'aria-label' => Yii::t('backend', 'Link'),
							'data-pjax' => '0',
						]);
				},
				'note' => static function (string $url, Issue $model): string {
					return Html::a('<i class="fa fa-comments" aria-hidden="true"></i>',
						['note/create', 'issueId' => $model->id],
						[
							'title' => Yii::t('issue', 'Create Issue Note'),
							'aria-label' => Yii::t('issue', 'Create Issue Note'),
						]
					);
				},
				'sms' => static function (string $url, Issue $model): string {
					return Html::a('<i class="fa fa-envelope" aria-hidden="true"></i>',
						['sms/push', 'id' => $model->id],
						[
							'title' => Yii::t('common', 'Send SMS'),
							'aria-label' => Yii::t('common', 'Send SMS'),
						]
					);
				},
			],
			'visibleButtons' => [
				'installment' => Yii::$app->user->can(Worker::ROLE_BOOKKEEPER),
				'note' => Yii::$app->user->can(Worker::PERMISSION_NOTE),
				'view' => static function (Issue $model) use ($searchModel): bool {
					return !$model->isArchived() || $searchModel->withArchive;
				},
				'link' => Yii::$app->user->can(Worker::PERMISSION_ISSUE_CREATE),
				'sms' => Yii::$app->user->can(Worker::PERMISSION_SMS),
				'delete' => Yii::$app->user->can(Worker::PERMISSION_ISSUE_DELETE),
			],
		],

	],
]);
?>