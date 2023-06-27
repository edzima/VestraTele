<?php

use backend\modules\issue\models\search\IssueArchiveSearch;
use backend\widgets\GridView;
use common\widgets\grid\SerialColumn;
use kartik\grid\ExpandRowColumn;

/* @var $this yii\web\View */
/* @var $searchModel IssueArchiveSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('issue', 'Archive');

$this->params['breadcrumbs'][] = ['label' => Yii::t('issue', 'Issues'), 'url' => ['issue/index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="issue-archive-index">


	<?= $this->render('_search', ['model' => $searchModel]); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'pjax' => true,
		'columns' => [
			['class' => SerialColumn::class,],

			[
				'class' => ExpandRowColumn::class,
				'value' => function () {
					return GridView::ROW_COLLAPSED;
				},
				'detailUrl' => ['issues'],
			],
			[
				'attribute' => 'archives_nr',
			],
			[
				'attribute' => 'max_stage_change_at',
				'label' => $searchModel->getAttributeLabel('max_stage_change_at'),
				'format' => 'date',
			],
			[
				'attribute' => 'count',
				'label' => $searchModel->getAttributeLabel('count'),
			],
		],
	]); ?>


</div>
