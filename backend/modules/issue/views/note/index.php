<?php

use backend\modules\issue\models\search\IssueNoteSearch;
use backend\widgets\GridView;
use backend\widgets\IssueColumn;
use common\widgets\grid\ActionColumn;

/* @var $this yii\web\View */
/* @var $searchModel IssueNoteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('issue', 'Issue Notes');
$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issues'), 'url' => ['issue/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issue-note-index">

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			['class' => IssueColumn::class],
			[
				'attribute' => 'user_id',
				'value' => 'user',
				'filter' => IssueNoteSearch::getUsersNames(),
			],
			'title',
			'description',
			'is_pinned:boolean',
			'publish_at:datetime',
			'created_at:datetime',
			'updated_at:datetime',
			['class' => ActionColumn::class],
		],
	]); ?>
</div>
