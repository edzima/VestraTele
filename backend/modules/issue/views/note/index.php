<?php

use backend\modules\issue\models\search\IssueNoteSearch;
use backend\widgets\GridView;
use backend\widgets\IssueColumn;
use common\models\issue\IssueNote;
use common\widgets\grid\ActionColumn;
use common\widgets\grid\AgentDataColumn;
use common\widgets\grid\DataColumn;
use common\widgets\grid\IssueStageColumn;
use common\widgets\grid\IssueTypeColumn;

/* @var $this yii\web\View */
/* @var $searchModel IssueNoteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('issue', 'Issue Notes');
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issues'), 'url' => ['issue/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-note-index">

	<?= $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => IssueColumn::class],
			[
				'class' => IssueTypeColumn::class,
				'attribute' => 'issueTypeId',
			],
			[
				'class' => AgentDataColumn::class,
				'value' => 'issue.agent',
			],
			[
				'class' => IssueStageColumn::class,
				'attribute' => 'issueStageId',
			],
			[
				'attribute' => 'type',
				'filter' => IssueNoteSearch::getTypesNames(),
				'value' => 'typeKindName',
				'noWrap' => true,
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'user_id',
				'value' => 'user',
				'filter' => IssueNoteSearch::getUsersNames(),
				'filterType' => GridView::FILTER_SELECT2,
				'filterWidgetOptions' => [
					'options' => [
						'placeholder' => $searchModel->getAttributeLabel('user_id'),
					],
				],
			],
			[
				'class' => DataColumn::class,
				'attribute' => 'updater_id',
				'value' => 'updater',
				'filter' => IssueNoteSearch::getUpdatersNames(),
				'filterType' => GridView::FILTER_SELECT2,
				'filterWidgetOptions' => [
					'options' => [
						'placeholder' => $searchModel->getAttributeLabel('updater_id'),
					],
				],
			],
			'title',
			'description',
			'is_pinned:boolean',
			'is_template:boolean',
			'publish_at:datetime',
			'created_at:datetime',
			'updated_at:datetime',
			[
				'class' => ActionColumn::class,
				'visibleButtons' => [
					'update' => function (IssueNote $note): bool {
						return !$note->isSms();
					},
					'delete' => function (IssueNote $note): bool {
						return Yii::$app->user->canDeleteNote($note);
					},
				],
			],
		],
	]); ?>
</div>
