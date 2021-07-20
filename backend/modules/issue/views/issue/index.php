<?php

use backend\helpers\Html;
use backend\modules\issue\models\search\IssueSearch;
use backend\widgets\CsvForm;
use backend\widgets\GridView;
use backend\widgets\IssueColumn;
use common\models\issue\Issue;
use common\models\user\User;
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
		<?= Yii::$app->user->can(User::PERMISSION_SUMMON)
			? Html::a(Yii::t('common', 'Summons'), ['/issue/summon/index'], ['class' => 'btn btn-warning'])
			: ''
		?>
		<?= Yii::$app->user->can(User::ROLE_BOOKKEEPER)
			? Html::a(Yii::t('backend', 'Settlements'), ['/settlement/calculation/index'], ['class' => 'btn btn-success'])
			: ''
		?>
	</p>

	<?= $this->render('_search', ['model' => $searchModel]) ?>

	<?= Yii::$app->user->can(User::PERMISSION_EXPORT)
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
				'contentOptions' => [
					'class' => 'ellipsis',
				],
				'options' => [
					'style' => 'width:200px',
				],
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'type_id',
				'filter' => IssueSearch::getTypesNames(),
				'value' => 'type.short_name',
				'contentOptions' => [
					'class' => 'bold-text text-center',
				],
				'options' => [
					'style' => 'width:80px',
				],
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'stage_id',
				'filter' => $searchModel->getStagesNames(),
				'value' => 'stage.short_name',
				'contentOptions' => [
					'class' => 'bold-text text-center',
				],
				'options' => [
					'style' => 'width:60px',
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
				'value' => 'entityResponsible.name',
				'options' => [
					'style' => 'width:200px',
				],
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'stage_change_at',
				'format' => 'date',
				'contentOptions' => [
					'class' => 'bold-text',
				],
				'options' => [
					'style' => 'width:90px',
				],
				'visible' => $searchModel->onlyDelayed,
			],
			[
				'class' => CustomerDataColumn::class,
				'value' => 'customer.fullName',
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'created_at',
				'format' => 'date',
				'contentOptions' => [
					'class' => 'bold-text',
				],
				'options' => [
					'style' => 'width:90px',
				],
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'updated_at',
				'format' => 'date',
				'contentOptions' => [
					'class' => 'bold-text',
				],
				'options' => [
					'style' => 'width:90px',
				],
			],
			[
				'class' => ActionColumn::class,
				'template' => '{installment} {view} {update} {delete}',
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
				],
				'visibleButtons' => [
					'installment' => Yii::$app->user->can(User::ROLE_ADMINISTRATOR),
					'view' => static function (Issue $model) use ($searchModel): bool {
						return !$model->isArchived() || $searchModel->withArchive;
					},
					'delete' => Yii::$app->user->can(User::ROLE_ADMINISTRATOR),
				],
			],

		],
	]); ?>
	<?php Pjax::end(); ?>
</div>
