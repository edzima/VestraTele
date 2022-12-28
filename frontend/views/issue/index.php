<?php

use common\models\issue\IssueInterface;
use common\models\user\Worker;
use common\modules\issue\IssueNoteColumn;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\AgentDataColumn;
use common\widgets\grid\CustomerDataColumn;
use common\widgets\grid\DataColumn;
use common\widgets\grid\IssueTypeColumn;
use frontend\helpers\Html;
use frontend\helpers\Url;
use frontend\models\search\IssueSearch;
use frontend\widgets\GridView;
use frontend\widgets\issue\StageChangeButtonDropdown;
use frontend\widgets\IssueColumn;
use kartik\select2\Select2;
use yii\bootstrap\Nav;
use yii\grid\SerialColumn;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel IssueSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Issues');
if ($searchModel->getParentType()) {
	$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $searchModel->getParentType()->name, Url::issuesParentType($searchModel->getParentType()->id)];
} else {
	$this->params['breadcrumbs'][] = $this->title;
}
$parentMenuItems = Html::issueParentTypeItems();

?>
<div class="issue-index">

	<?php if (empty($parentMenuItems)): ?>
		<h1><?= Html::encode($this->title) ?></h1>
	<?php else: ?>
		<h1 class="title-with-nav">
			<?= Html::encode($this->title) ?>
			<?= Nav::widget([
				'items' => $parentMenuItems,
				'options' => [
					'class' => 'nav nav-pills',
				],
			]); ?>
		</h1>

	<?php endif; ?>

	<p>
		<?= Html::a(Yii::t('frontend', 'Search issue user'), ['user'], ['class' => 'btn btn-info']) ?>
		<?= Html::a(Yii::t('frontend', 'Yours settlements'), ['/settlement/index'], ['class' => 'btn btn-success']) ?>
		<?= Html::a(Yii::t('settlement', 'Pays'), ['/pay/index'], ['class' => 'btn btn-success']) ?>

		<?= Yii::$app->user->can(Worker::PERMISSION_SUMMON)
			? Html::a(Yii::t('issue', 'Summons'), ['/summon/index'], ['class' => 'btn btn-warning'])
			: ''
		?>

		<?= Yii::$app->user->can(Worker::PERMISSION_PAY_RECEIVED)
			? Html::a(Yii::t('settlement', 'Received pays'), ['/pay-received/index'], ['class' => 'btn btn-primary'])
			: ''
		?>
	</p>
	<?php Pjax::begin(); ?>
	<?= $this->render('_search', ['model' => $searchModel]) ?>

	<?php
	//@todo remove this after migrate BS4 (add data-boundary="viewport")
	//@see https://stackoverflow.com/questions/26018756/bootstrap-button-drop-down-inside-responsive-table-not-visible-because-of-scroll#answer-51992907
	$this->registerJs("$('.table-responsive').on('show.bs.dropdown', function () {
	     $('.table-responsive').css('overflow', 'inherit' );
		});

		$('.table-responsive').on('hide.bs.dropdown', function () {
            $('.table-responsive').css( 'overflow', 'auto' );
		});
		"
	);
	?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'emptyText' => $searchModel->hasExcludedArchiveStage() ? Yii::t('issue', 'Archive is Excluded. Check in them.') : null,
		'columns' => [
			['class' => SerialColumn::class], // @todo to approval
			[
				'class' => IssueColumn::class,
				'filterInputOptions' => [
					'class' => 'input-sm form-control',
					'id' => null,
				],
			],
			[
				'class' => IssueTypeColumn::class,
				'attribute' => 'type_id',
				'noWrap' => true,
			],

			[
				'class' => DataColumn::class,
				'attribute' => 'entity_responsible_id',
				'label' => $searchModel->getAttributeLabel('entity_responsible_id'),
				'filter' => IssueSearch::getEntityNames(),
				'filterType' => GridView::FILTER_SELECT2,
				'filterWidgetOptions' => [
					'pluginOptions' => [
						'allowClear' => true,
						'dropdownAutoWidth' => true,

					],
					'options' => [
						'placeholder' => $searchModel->getAttributeLabel('entity_responsible_id'),
					],
					'size' => Select2::SIZE_SMALL,
				],
				'value' => 'issue.entityResponsible.name',
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'stage_id',
				'label' => $searchModel->getAttributeLabel('stage_id'),
				'filter' => $searchModel->getStagesNames(),
				'filterType' => GridView::FILTER_SELECT2,
				'filterWidgetOptions' => [
					'options' => [
						'placeholder' => $searchModel->getAttributeLabel('stage_id'),
					],
					'pluginOptions' => [
						'allowClear' => true,
						'dropdownAutoWidth' => true,
					],
					'size' => Select2::SIZE_SMALL,
				],
				'value' => static function (IssueInterface $model): string {
					if (Yii::$app->user->can(Worker::PERMISSION_ISSUE_STAGE_CHANGE)) {
						return StageChangeButtonDropdown::widget([
							'model' => $model,
							'label' => $model->getIssueStage()->name,
							'containerOptions' => [
								'class' => 'd-inline-flex',
							],
							'returnUrl' => Url::to('/issue/index'),
							'options' => [
								'class' => 'btn btn-default btn-sm',
								'title' => Yii::t('issue', 'Change Stage'),
								'aria-label' => Yii::t('issue', 'Change Stage'),
								'data-pjax' => 0,
							],
						]);
					}
					return Html::encode($model->getIssueStage()->short_name);
				},
				'format' => 'raw',
				'contentBold' => true,
				'contentCenter' => true,
			],
			[
				'class' => AgentDataColumn::class,
				'noWrap' => false,
				'value' => 'issue.agent.fullName',
				'filterWidgetOptions' => [
					'size' => Select2::SIZE_SMALL,
				],
			],
			[
				'class' => CustomerDataColumn::class,
				'attribute' => 'customerName',
				'value' => 'issue.customer.fullName',
				'noWrap' => false,
				'filterInputOptions' => [
					'class' => 'input-sm form-control',
					'id' => null,
				],
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'customerPhone',
				'value' => 'issue.customer.profile.phone',
				'format' => 'tel',
				'width' => '124px',
				'label' => Yii::t('common', 'Phone number'),
				'noWrap' => true,
				'filterInputOptions' => [
					'class' => 'input-sm form-control',
					'id' => null,
					'placeholder' => Yii::t('common', 'Phone number'),
				],
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'issue.created_at',
				'format' => 'date',
				'noWrap' => true,
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'issue.updated_at',
				'format' => 'date',
				'noWrap' => true,
			],
			[
				'class' => IssueNoteColumn::class,
			],
			[
				'class' => ActionColumn::class,
				'template' => '{note} {sms} {view}',
				'visibleButtons' => [
					'view' => static function (IssueInterface $model) use ($searchModel) {
						return !$model->getIssueModel()->isArchived() || $searchModel->withArchive;
					},
					'note' => Yii::$app->user->can(Worker::PERMISSION_NOTE),
					'sms' => Yii::$app->user->can(Worker::PERMISSION_SMS),

				],
				'buttons' => [
					'note' => static function (string $url, IssueInterface $model): string {
						return Html::a('<i class="fa fa-comments" aria-hidden="true"></i>',
							['note/issue', 'id' => $model->getIssueId()],
							[
								'title' => Yii::t('issue', 'Create Issue Note'),
								'aria-label' => Yii::t('issue', 'Create Issue Note'),
							]
						);
					},
					'sms' => static function (string $url, IssueInterface $model): string {
						return Html::a('<i class="fa fa-envelope" aria-hidden="true"></i>',
							['issue-sms/push', 'id' => $model->getIssueId()],
							[
								'title' => Yii::t('common', 'Send SMS'),
								'aria-label' => Yii::t('common', 'Send SMS'),
							]
						);
					},
				],
			],
		],
	]) ?>
	<?php Pjax::end(); ?>
</div>
