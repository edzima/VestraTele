<?php

use backend\helpers\Url;
use backend\modules\issue\models\search\IssueSearch;
use backend\modules\issue\widgets\StageChangeButtonDropdown;
use backend\widgets\GridView;
use backend\widgets\IssueColumn;
use common\assets\TooltipAsset;
use common\helpers\Html;
use common\models\issue\Issue;
use common\models\user\User;
use common\models\user\Worker;
use common\modules\issue\IssueNoteColumn;
use common\modules\issue\widgets\IssueClaimCompanyColumn;
use common\modules\issue\widgets\IssuePaysColumnWidget;
use common\modules\issue\widgets\IssueSummonsColumn;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\CustomerDataColumn;
use common\widgets\grid\DataColumn;
use common\widgets\grid\IssueStageColumn;
use common\widgets\grid\IssueTypeColumn;
use common\widgets\grid\SerialColumn;
use kartik\grid\CheckboxColumn;
use kartik\select2\Select2;
use yii\bootstrap\Alert;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $searchModel IssueSearch */
/* @var $dataProvider ActiveDataProvider */
/* @var $gridId string */

//@todo remove this after migrate BS4 (add data-boundary="viewport")
//@see https://stackoverflow.com/questions/26018756/bootstrap-button-drop-down-inside-responsive-table-not-visible-because-of-scroll#answer-51992907
$js = <<<JS
var table = $('.table-responsive');
table.on('show.bs.dropdown', function () { 
	table.css('overflow', 'inherit' );
});

table.on('hide.bs.dropdown', function () {
    table.css( 'overflow', 'auto' );
})
JS;

$this->registerJs($js);

?>


<p>
	<?= $searchModel->withClaimsSum && ($claimSum = $searchModel->claimsSum($dataProvider->query)) > 0
		? (Yii::t('backend', 'Claims Sum: {sum}', [
			'sum' => Yii::$app->formatter->asCurrency($claimSum),
		]))
		: '' ?>

</p>


<?= GridView::widget([
	'id' => $gridId,
	'dataProvider' => $dataProvider,
	'showPageSummary' => Yii::$app->user->can(User::ROLE_ADMINISTRATOR),
	'pageSummaryPosition' => GridView::POS_BOTTOM,
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
	'emptyText' => $searchModel->hasExcludedArchiveStage() && ($totalCount = $searchModel->getTotalCountWithArchive()) > 0
		? Alert::widget([
			'body' =>
				Html::a(Yii::t('issue', 'The archive is excluded. Matching Issues found in it: {count}.', [
					'count' => $totalCount,
				]),
					Url::to(['archive']) . '?' . Yii::$app->request->queryString
				),
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
			'withAdditionalDateAt' => true,
		],
		[
			'class' => CustomerDataColumn::class,
			'value' => 'customer.fullName',
			'attribute' => 'customerName',
		],
		[
			'class' => IssueStageColumn::class,
			'attribute' => 'stage_id',
			'filter' => $searchModel->getIssueStagesNames(),
			'value' => static function (Issue $model): string {
				return StageChangeButtonDropdown::widget([
					'model' => $model,
					'label' => $model->stage->short_name,
					'containerOptions' => [
						'class' => 'd-inline-flex',
						TooltipAsset::DEFAULT_ATTRIBUTE_NAME => $model->stage->name,
					],
					'returnUrl' => Url::current(),
					'options' => [
						'class' => 'btn btn-default btn-sm',
						'title' => Yii::t('issue', 'Change Stage'),
						'aria-label' => Yii::t('issue', 'Change Stage'),
						'data-pjax' => 0,
					],
				]);
			},
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
					'multiple' => true,
				],
			],
		],
		[
			'class' => DataColumn::class,
			'attribute' => 'entity_responsible_id',
			'filter' => $searchModel->getEntityResponsibleNames(),
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
			'contentOptions' => [
				'class' => 'mw-120-md',
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
			'pageSummary' => true,
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
			'template' => '{installment} {link} {note} {sms} {tag} {view} {update} {delete}',
			'buttons' => [
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
				'tag' => static function (string $url, Issue $model): string {
					return Html::a(Html::icon('tag'),
						['tag/issue', 'issueId' => $model->id],
						[
							'title' => Yii::t('common', 'Tags'),
							'aria-label' => Yii::t('common', 'Tags'),
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
				'tag' => Yii::$app->user->can(Worker::PERMISSION_ISSUE_CREATE),
				'delete' => Yii::$app->user->can(Worker::PERMISSION_ISSUE_DELETE),
			],
		],

	],
]);
?>

