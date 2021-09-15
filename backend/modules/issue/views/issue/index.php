<?php

use backend\helpers\Html;
use backend\modules\issue\models\search\IssueSearch;
use backend\modules\issue\widgets\StageChangeButtonDropdown;
use backend\widgets\CsvForm;
use backend\widgets\GridView;
use backend\widgets\IssueColumn;
use common\models\issue\Issue;
use common\models\user\Worker;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\CustomerDataColumn;
use common\widgets\grid\DataColumn;
use kartik\grid\SerialColumn;
use yii\data\ActiveDataProvider;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel IssueSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('backend', 'Issues');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="issue-index relative">
	<?php Pjax::begin(); ?>

	<p>
		<?= Yii::$app->user->can(Worker::PERMISSION_SUMMON)
			? Html::a(Yii::t('common', 'Summons'), ['/issue/summon/index'], ['class' => 'btn btn-warning'])
			: ''
		?>
		<?= Yii::$app->user->can(Worker::PERMISSION_NOTE)
			? Html::a(Yii::t('issue', 'Issue Notes'), ['note/index'], ['class' => 'btn btn-info'])
			: ''
		?>
		<?= Yii::$app->user->can(Worker::ROLE_BOOKKEEPER)
			? Html::a(Yii::t('backend', 'Settlements'), ['/settlement/calculation/index'], ['class' => 'btn btn-success'])
			: ''
		?>
	</p>

	<?= $this->render('_search', ['model' => $searchModel]) ?>

	<?= Yii::$app->user->can(Worker::PERMISSION_EXPORT)
		? CsvForm::widget()
		: ''
	?>

	<?= GridView::widget([
		'id' => 'issues-list',
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'resizableColumns' => false,
		'tableOptions' => [
			'class' => 'table-fixed-layout',
		],
		'columns' => [
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
				'class' => DataColumn::class,
				'attribute' => 'type_id',
				'filter' => IssueSearch::getTypesNames(),
				'value' => 'type.short_name',
				'contentBold' => true,
				'contentCenter' => true,
				'options' => [
					'style' => 'width:80px',
				],
			],
			[
				'class' => CustomerDataColumn::class,
				'value' => 'customer.fullName',
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
				'ellipsis' => true,
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
					'style' => 'width:90px',
				],
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
					'style' => 'width:90px',
				],
			],
			[
				'class' => ActionColumn::class,
				'template' => '{installment} {stage} {note} {view} {update} {delete}',
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
					'note' => static function (string $url, Issue $model): string {
						return Html::a('<i class="fa fa-comments" aria-hidden="true"></i>',
							['note/create', 'issueId' => $model->id],
							[
								'title' => Yii::t('issue', 'Create Issue Note'),
								'aria-label' => Yii::t('issue', 'Create Issue Note'),
							]
						);
					},
				],
				'visibleButtons' => [
					'installment' => Yii::$app->user->can(Worker::ROLE_ADMINISTRATOR),
					'note' => Yii::$app->user->can(Worker::PERMISSION_NOTE),
					'view' => static function (Issue $model) use ($searchModel): bool {
						return !$model->isArchived() || $searchModel->withArchive;
					},
					'delete' => Yii::$app->user->can(Worker::PERMISSION_ISSUE_DELETE),
				],
			],

		],
	]); ?>
	<?php Pjax::end(); ?>
</div>
